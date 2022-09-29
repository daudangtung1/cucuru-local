<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Utils\FolderHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use function Composer\Autoload\includeFile;

class SyncLanguage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "sync:language {--db} {--file}";

//        protected $op

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command sync language.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->option('db') && !$this->option('file')) {
            $this->alert('Missing --db or --file in command.');
            return false;
        }

        if ($this->option('file')) {
            $this->syncFileToDb();
        }

        if ($this->option('db')) {
            $this->syncDbToFile();
        }
    }

    private function syncFileToDb()
    {
        $locales = FolderHelper::getInstance()->getLocaleFolders();

        if (empty($locales)) {
            $this->alert("Not found any locale!");
            return false;
        }

        $languageInDB = Language::all();
        $localeLanguage = [];

        foreach ($languageInDB as $languageItem) {
            $localeLanguage[$languageItem['name']][] = $languageItem->toArray();
        }

        $languageDataInsert = [];
        $languageDataUpdate = [];

        foreach ($locales as $locale) {
            $fileInfo = FolderHelper::getInstance()->getAllLocaleFileInFolder($locale);
            if (empty($fileInfo)) continue;
            $languageByLocale = $localeLanguage[$locale] ?? [];

            if (!empty($languageByLocale)) {
                $languageByLocale = array_combine(array_column($languageByLocale, 'key'), $languageByLocale);
            }

            foreach ($fileInfo as $file) {
                if (empty($file)) continue;
                $fileData = FolderHelper::getInstance()->getContentLocaleFile($file);

                if (empty($fileData)) continue;

                // Sync file to DB
                foreach ($fileData as $key => $value) {
                    if (!isset($languageByLocale[$key])) {
                        $languageDataInsert[] = [
                            'key' => $key,
                            'name' => $locale,
                            'value' => $value,
                            'file' => $file['basePath'],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    } elseif ($languageByLocale[$key]['value'] !== $value) {
                        $languageDataUpdate[$languageByLocale[$key]['id']] = $value;
                    }
                }
            }
        }

        try {
            DB::beginTransaction();
            Language::insert($languageDataInsert);

            if (!empty($languageDataUpdate)) {
                foreach ($languageDataUpdate as $languageId => $value) {
                    Language::where(['id' => $languageId])->update(['value' => $value]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage());
            return false;
        }

        $this->components->info("Sync language from file to database success.");
        $this->line("- (" . count($languageDataInsert) . ") row insert.");
        $this->line("- (" . count($languageDataUpdate) . ") row update.");
    }

    private function syncDbToFile()
    {
        $languageInDB = Language::all()->toArray();

        if (empty($languageInDB)) {
            $this->alert('Languages table is empty!');
            return false;
        }

        $fileInLangFolder = [];
        $newFiles = [];
        $fileAndDataNeedDelete = [];

        foreach ($languageInDB as $lang) {
            $filePath = resource_path('lang\\' . $lang['name'] . '\\' . $lang['file']);

            if (!in_array($filePath, array_keys($fileInLangFolder)) &&
                file_exists($filePath)) {
                $fileInLangFolder[$filePath] = $fileAndDataNeedDelete[$filePath] = include($filePath);
            }

            if (!file_exists($filePath)) {
                if (!File::isDirectory(resource_path('lang\\' . $lang['name']))) {
                    File::makeDirectory(resource_path('lang\\' . $lang['name']));
                }

                File::put($filePath, "", true);
                $fileInLangFolder[$filePath] = [];
                $newFiles[$filePath] = [];
            }

            $splitKey = explode('.', $lang['key']);
            unset($splitKey[0]);
            $filePathWithoutExt = str_replace('.php', '', $lang['file']);

            $key = $filePathWithoutExt . '.' .
                implode('.', array_diff($splitKey, explode('\\', $filePathWithoutExt)));
            $attributeInArray = implode('.', array_diff($splitKey, explode('\\', $filePathWithoutExt)));

            if (in_array($filePath, array_keys($newFiles))) {
                Arr::set($newFiles[$filePath], $attributeInArray, $lang['value']);
            } else {
                $getValueOfLang = __($key, [], $lang['name']);
                if (trans()->has($key)) {
                    if ($getValueOfLang !== $lang['value']) {
                        $fileInLangFolder[$filePath]['has_changed'] = true;
                        Arr::set($fileInLangFolder[$filePath], $attributeInArray, $lang['value']);
                    }

                    Arr::forget($fileAndDataNeedDelete[$filePath], $attributeInArray);
                    if (empty(Arr::get($fileAndDataNeedDelete[$filePath], $attributeInArray))) {
                        $splitAttribute = explode('.', $attributeInArray);
                        $attributeNotChange = implode('.', array_slice($splitAttribute, 0, count($splitAttribute) - 1));
                        Arr::forget($fileAndDataNeedDelete[$filePath], $attributeNotChange);
                    }
                } else {
                    Arr::set($fileInLangFolder[$filePath], $attributeInArray, $lang['value']);
                }
            }
        }


        if (!empty($fileAndDataNeedDelete)) {
            foreach ($fileAndDataNeedDelete as $filePath => $content) {
                $fileAndDataNeedDelete[$filePath] = convert_associative_array_to_flatten_array($content, '');
            }
        }
        $fileAndDataNeedDelete = array_filter($fileAndDataNeedDelete);

        if (!empty($fileAndDataNeedDelete)) {
            foreach ($fileAndDataNeedDelete as $filePath => $content) {
                $fileInLangFolder[$filePath]['has_changed'] = true;
                foreach ($content as $attribute => $value) {
                    Arr::forget($fileInLangFolder[$filePath], $attribute);
                }
            }
        }

        if (!empty($newFiles)) {
            foreach ($newFiles as $filePath => $content) {
                File::put($filePath, print_r("<?php \n\n" .
                    "return " .
                        $this->var_export($content, true)
                    . ";", true));
            }
        }

        $numberFileUpdate = 0;
        foreach ($fileInLangFolder as $filePath => $content) {
            if (isset($content['has_changed'])) {
                $numberFileUpdate++;
                unset($content['has_changed']);
                File::put($filePath, print_r("<?php \n\n" .
                    "return " .
                    $this->var_export($content, true)
                    . ";", true));
            }
        }

        $this->components->info("Sync language from database to file success.");
        if (!empty($newFiles)) $this->line("- (" . count($newFiles) . ") file was insert.");
        $this->line("- ($numberFileUpdate) file was update.");

        return true;
    }

    private function var_export($expression, $return = FALSE)
    {
        $export = var_export($expression, TRUE);
        $export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $export);
        $array = preg_split("/\r\n|\n|\r/", $export);
        $array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [NULL, ']$1', ' => ['], $array);
        $export = join(PHP_EOL, array_filter(["["] + $array));

        if ((bool)$return) return $export; else echo $export;
    }
}
