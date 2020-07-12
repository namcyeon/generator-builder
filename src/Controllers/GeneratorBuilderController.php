<?php

namespace namcyeon\GeneratorBuilder\Controllers;

use App\Http\Controllers\Controller;
use Artisan;
use File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use namcyeon\GeneratorBuilder\Requests\BuilderGenerateRequest;
use Request;
use Response;

class GeneratorBuilderController extends Controller
{
    public function __construct()
    {
    }

    public function builder()
    {
        return view(config('namcyeon.generator_builder.views.builder'));
    }
    
    public function fieldTemplate()
    {
        return view(config('namcyeon.generator_builder.views.field-template'));
    }

    public function relationFieldTemplate()
    {
        return view(config('namcyeon.generator_builder.views.relation-field-template'));
    }

    public function generate(BuilderGenerateRequest $request)
    {
        $data = $request->all();
        $module_path = config('modules.paths.modules');
        $sourceDir = __DIR__ . "/../../module_template/";
        // Validate fields
        if (isset($data['fields'])) {
            $this->validateFields($data['fields']);

            // prepare foreign key
            $isAnyForeignKey =  collect($data['fields'])->filter(function ($field) {
                return $field['isForeign'] == true;
            });
            if (count($isAnyForeignKey)) {
                $data['fields'] = $this->prepareForeignKeyData($data['fields']);
            }
        }

        // prepare relationship
        if (isset($data['fields']) && !empty($data['relations'])) {
            $data = $this->prepareRelationshipData($data);
        }
        
        $destinationDir = $module_path .'/' . $data['moduleName'];

        // Delete module if exists
        if(File::exists($destinationDir)) {
            \Artisan::call('module:delete', [
                'module' => $data['moduleName']
            ]);
        }
        $MODULEFOLDER = $data['moduleFolder'];
        $MODULENAME = $data['moduleName'];
        $LOWERNAME = strtolower($data['moduleName']);
        $TABLENAME = strtolower($data['modelName']);
        $MIGRATENAME = str_replace(' ', '', ucwords(str_replace('_', ' ', $data['modelName'])));
        $MODULETITLE = $data['tableName'];
        $MIGRATE = '';
        $MIGRATEFOREIGN = '';
        $CRUDCOLUMN = '';
        $CRUDVIEWFIELD = '';
        $CRUDVIEWFIELD2 = '';
        $CUSTOMCOLUMN = '';
        $FOREIGN = '';
        $CRUDTEMPLATE = 'default';
        if (!empty($data['crudTemplate']))
            $CRUDTEMPLATE = $data['crudTemplate'];
        $CRUDALL = '';
        foreach ($data['fields'] as $field) {
            if (empty($field['label'])) {
                $field['label'] = ucfirst($field['name']);
            }
            $field['primary'] = true;
            if (empty($field['primary'])) {
                $field['primary'] = false;
            }

            $CRUDCOLUMN .= "        \$this->module_column[] = ['data' =>  '".$field['name']."', 'name' => '".$field['name']."', 'title' => '".$field['label']."', 'class' => 'text-left'];" . PHP_EOL;

            $CRUDVIEWFIELD .= "'".$field['name']."',";
            $CRUDVIEWFIELD2 .= "'".$field['name']."',";

            if (!empty($field['position'])) {
                $CRUDALL .= "        \$this->module_fields['".$field['position']."'][] = array(" . PHP_EOL .
                "            'name' => '".$field['name']."'," . PHP_EOL .
                "            'type' => '".$field['type']."'," . PHP_EOL .
                "            'class'  => '".$field['col']."'," . PHP_EOL .
                "            'required' => ".$field['primary']."," . PHP_EOL;
                if (!empty($field['data']))
                    $CRUDALL .= "            'data' => ".$field['data']."," . PHP_EOL;
                $CRUDALL .= "            'label' => '".$field['label']."');" . PHP_EOL;
            } else {
                $CRUDALL .= "        \$this->module_fields[] = array(" . PHP_EOL .
                "            'name' => '".$field['name']."'," . PHP_EOL .
                "            'type' => '".$field['type']."'," . PHP_EOL .
                "            'class'  => '".$field['col']."'," . PHP_EOL .
                "            'required' => ".$field['primary']."," . PHP_EOL;
                if (!empty($field['data']))
                    $CRUDALL .= "            'data' => ".$field['data']."," . PHP_EOL;
                $CRUDALL .= "            'label' => '".$field['label']."');" . PHP_EOL;
            }

            if ($field['type'] == 'image') {
                $CUSTOMCOLUMN .= PHP_EOL . "                        ->editColumn('".$field['name']."', '<img src=\"{{\$".$field['name']."}}\" style=\"width:50px;height:50px;\">')";
            }
            if ($field['type'] == 'toggle-switch') {
                $CUSTOMCOLUMN .= PHP_EOL . "                        ->editColumn('".$field['name']."', '<label class=\"switch switch-3d switch-primary\"><input type=\"checkbox\" class=\"switch-input\" {{\$".$field['name']." ? \'checked\' : \' \'}}><span class=\"switch-label\"></span><span class=\"switch-handle\"></span></label>')";
            }
            if (!empty($field['foreignTable'])) {
                $foreighInfo = explode(',', $field['foreignTable']);
                $FOREIGN .= "    public function " . trim($foreighInfo[0]) . "()" . PHP_EOL .
                            "    {" . PHP_EOL . "        return \$this->belongsTo('" . trim($foreighInfo[1]) . "');" . PHP_EOL . "    }" . PHP_EOL;
                //$MIGRATE .= "            \$table->foreign('".$field['name']."')->references('id')->on('".trim($foreighInfo[2])."');" . PHP_EOL;
                $MIGRATE .= "            \$table->bigInteger('".$field['name']."')->unsigned();" . PHP_EOL;
                $MIGRATEFOREIGN .= "            \$table->foreign('".$field['name']."')->references('id')->on('".trim($foreighInfo[2])."');";
            } else {
                $MIGRATE .= "            \$table->".$field['dbType']."('".$field['name']."');" . PHP_EOL;
            }
        }
        File::makeDirectory($destinationDir, 493, true);
        File::copyDirectory($sourceDir, $destinationDir);

        $paths = [
            $destinationDir . "/composer.json",
            $destinationDir . "/module.json",
            $destinationDir . "/Routes/web.stub",
            $destinationDir . "/Routes/api.stub",
            $destinationDir . "/Resources/views/frontend/index.blade.stub",
            $destinationDir . "/Providers/ModuleServiceProvider.stub",
            $destinationDir . "/Providers/RouteServiceProvider.stub",
            $destinationDir . "/Http/Middleware/GenerateMenus.stub",
            $destinationDir . "/Http/Controllers/Backend/ModuleController.stub",
            $destinationDir . "/Http/Controllers/Frontend/ModuleController.stub",
            $destinationDir . "/Http/Requests/Backend/ModuleRequest.stub",
            $destinationDir . "/Http/Requests/Frontend/ModuleRequest.stub",
            $destinationDir . "/Models/Module.stub",
            $destinationDir . "/Database/factories/ModuleFactory.stub",
            $destinationDir . "/Database/Migrations/create_table.stub",
            $destinationDir . "/Database/Seeders/ModuleDatabaseSeeder.stub",
            $destinationDir . "/Config/config.stub",
        ];
        foreach ($paths as $path) {
            $contentGet = file_get_contents($path);
            $contentGet = str_replace('$MODULENAME$', $MODULENAME, $contentGet);
            $contentGet = str_replace('$LOWERNAME$', $LOWERNAME, $contentGet);
            $contentGet = str_replace('$TABLENAME$', $TABLENAME, $contentGet);
            $contentGet = str_replace('$MODULETITLE$', $MODULETITLE, $contentGet);
            $contentGet = str_replace('$MIGRATE$', $MIGRATE, $contentGet);
            $contentGet = str_replace('$CRUDVIEWFIELD$', $CRUDVIEWFIELD, $contentGet);
            $contentGet = str_replace('$CRUDVIEWFIELD2$', $CRUDVIEWFIELD2, $contentGet);
            $contentGet = str_replace('$CRUDTEMPLATE$', $CRUDTEMPLATE, $contentGet);
            $contentGet = str_replace('$CRUDCOLUMN$', $CRUDCOLUMN, $contentGet);
            $contentGet = str_replace('$CRUDALL$', $CRUDALL, $contentGet);
            $contentGet = str_replace('$CUSTOMCOLUMN$', $CUSTOMCOLUMN, $contentGet);
            $contentGet = str_replace('$MIGRATENAME$', $MIGRATENAME, $contentGet);
            $contentGet = str_replace('$FOREIGN$', $FOREIGN, $contentGet);
            $contentGet = str_replace('$MIGRATEFOREIGN$', $MIGRATEFOREIGN, $contentGet);
            file_put_contents($path, $contentGet);

            $newpath = str_replace('ModuleController', $MODULENAME . 'Controller', $path);
            $newpath = str_replace('ModuleRequest', $MODULENAME . 'Request', $newpath);
            $newpath = str_replace('Module.stub', $MODULENAME . '.stub', $newpath);
            $newpath = str_replace('ModuleServiceProvider', $MODULENAME . 'ServiceProvider', $newpath);
            $newpath = str_replace('ModuleFactory', $MODULENAME . 'Factory', $newpath);
            $newpath = str_replace('ModuleDatabaseSeeder', $MODULENAME . 'DatabaseSeeder', $newpath);
            $newpath = str_replace('create_table.stub', date('Y').'_'.date('m').'_'.date('d').'_062135_create_' . $TABLENAME . '_table.stub', $newpath);
            $newpath = str_replace('.stub', '.php', $newpath);
            File::move($path, $newpath);
        }
        // $res = Artisan::call($data['commandType'], [
        //     'model'         => $data['modelName'],
        //     '--jsonFromGUI' => json_encode($data),
        // ]);

        return Response::json("Files created successfully");
    }

    public function rollback()
    {
        $data = Request::all();
        $input = [
            'model' => $data['modelName'],
            'type'  => $data['commandType'],
        ];

        if (!empty($data['prefix'])) {
            $input['--prefix'] = $data['prefix'];
        }

        Artisan::call('namcyeon:rollback', $input);

        return Response::json(['message' => 'Files rollback successfully'], 200);
    }

    public function generateFromFile()
    {
        $data = Request::all();

        /** @var UploadedFile $file */
        $file = $data['schemaFile'];
        $filePath = $file->getRealPath();
        $extension = $file->getClientOriginalExtension(); // getting file extension
        if ($extension != 'json') {
            throw new \Exception('Schema file must be Json');
        }

        Artisan::call($data['commandType'], [
            'model'        => $data['modelName'],
            '--fieldsFile' => $filePath,
        ]);

        return Response::json(['message' => 'Files created successfully'], 200);
    }

    private function validateFields($fields)
    {
        $fieldsGroupBy = collect($fields)->groupBy(function ($item) {
            return strtolower($item['name']);
        });

        $duplicateFields = $fieldsGroupBy->filter(function (Collection $groups) {
            return $groups->count() > 1;
        });
        if (count($duplicateFields)) {
            throw new \Exception('Duplicate fields are not allowed');
        }

        return true;
    }

    private function prepareRelationshipData($inputData)
    {
        foreach ($inputData['relations'] as $inputRelation) {
            $relationType = $inputRelation['relationType'];
            $relation = $relationType;
            if (isset($inputRelation['foreignModel'])) {
                $relation .= ','.$inputRelation['foreignModel'];
            }
            if ($relationType == 'mtm') {
                if (isset($inputRelation['foreignTable'])) {
                    $relation .= ','.$inputRelation['foreignTable'];
                } else {
                    $relation .= ',';
                }
            }
            if (isset($inputRelation['foreignKey'])) {
                $relation .= ','.$inputRelation['foreignKey'];
            }
            if (isset($inputRelation['localKey'])) {
                $relation .= ','.$inputRelation['localKey'];
            }

            $inputData['fields'][] = [
                'type'     => 'relation',
                'relation' => $relation,
            ];
        }
        unset($inputData['relations']);

        return $inputData;
    }

    private function prepareForeignKeyData($fields)
    {
        $updatedFields = [];
        foreach ($fields as $field) {
            if ($field['isForeign'] == true) {
                if (empty($field['foreignTable'])) {
                    throw new Exception('Foreign table required');
                }
                $inputs = explode(',', $field['foreignTable']);
                $foreignTableName = array_shift($inputs);
                // prepare dbType
                $dbType = $field['dbType'];
                $dbType .= ':unsigned:foreign';
                $dbType .= ','.$foreignTableName;
                if (!empty($inputs)) {
                    $dbType .= ','.$inputs['0'];
                } else {
                    $dbType .= ',id';
                }
                $field['dbType'] = $dbType;
            }
            $updatedFields[] = $field;
        }

        return $updatedFields;
    }

//    public function availableSchema()
//    {
//        $schemaFolder = config('infyom.laravel_generator.path.schema_files', base_path('resources/model_schemas/'));
//
//        if (!File::exists($schemaFolder)) {
//            return [];
//        }
//
//        $files = File::allFiles($schemaFolder);
//
//        $modelNames = [];
//
//        foreach ($files as $file) {
//            if(File::extension($file) == "json") {
//                $modelNames[] = File::name($file);
//            }
//        }
//
//        return Response::json($modelNames);
//    }
//
//    public function retrieveSchema($schema)
//    {
//        $schemaFolder = config('infyom.laravel_generator.path.schema_files', base_path('resources/model_schemas/'));
//
//        $filePath = $schemaFolder . $schema . ".json";
//
//        if (!File::exists($filePath)) {
//            return Response::json('not found', 402);
//        }
//
//        return Response::json(json_decode(File::get($filePath)));
//    }
}