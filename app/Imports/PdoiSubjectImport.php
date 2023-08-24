<?php

namespace App\Imports;

use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Models\EkatteArea;
use App\Models\EkatteMunicipality;
use App\Models\EkatteSettlement;
use App\Models\PdoiResponseSubject;
use App\Models\RzsSection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Validation\Rule;

class PdoiSubjectImport implements ToCollection
{

    //validation variables
    private array $admLevels;
    private array $admLevelsRuleIn;
    private array $areas;
    private array $areasRuleIn;
    private array $municipality;
    private array $municipalityRuleIn;
    private array $settlement;
    private array $settlementRuleIn;
    private array $responseSubjects;
    private array $responseSubjectsRuleIn;

    public function __construct()
    {
        $this->admLevels = RzsSection::get()->pluck('id', 'adm_level')->toArray();
        $this->admLevelsRuleIn = array_flip($this->admLevels);
        $this->areas = EkatteArea::get()->pluck('id', 'oblast')->toArray();
        $this->areasRuleIn = array_flip($this->areas);
        $this->municipality = EkatteMunicipality::get()->pluck('id', 'obstina')->toArray();
        $this->municipalityRuleIn = array_flip($this->municipality);
        $this->settlement = EkatteSettlement::get()->pluck('id', 'ekatte')->toArray();
        $this->settlementRuleIn = array_flip($this->settlement);
        $this->responseSubjects = PdoiResponseSubject::get()->pluck('id', 'eik')->toArray();
        $this->responseSubjectsRuleIn = array_flip($this->responseSubjects);
    }

    public function collection(Collection $collection): void
    {
        $hasUpdates   = 0;
        $hasInserts   = 0;
        $errors   = [];

        //Loop file rows
        foreach ($collection as $row_number => $row) {
            //skip empty rows. using filter because SkipsEmptyRows skip row index and messages are not correct at the end
            if( !$row->filter()->isNotEmpty() ) {
                continue;
            }

            $row = $row->toArray();
            //skip headers
            if ($row_number < 1) continue;

            //validate row columns count
            if( sizeof($row) != 14 ) {
                continue;
            }

            //validation rules
            $validator = Validator::make($row, $this->rules(), [], $this->attributes());
            if( $validator->fails() ) {
                $errors[$row_number + 1] = $validator->errors()->all();
                continue;
            }

            $validated = $validator->validated();

            $existingItem = PdoiResponseSubject::IsManual()->where('eik', $validated[0])->first();
            $admLevel = array_keys($this->admLevels, $validated[3]);
            $parent = array_keys($this->responseSubjects, $validated[4]);
            $area = array_keys($this->areas, $validated[6]);
            $municipality = array_keys($this->municipality, $validated[7]);
            $settlement = array_keys($this->settlement, $validated[8]);
            $court = array_keys($this->responseSubjects, $validated[11]);

            $fields = [
                'email' => $validated[2],
                'adm_level' => sizeof($admLevel) ? $admLevel[0] : null,
                'parent_id' => sizeof($parent) ? $parent[0] : null,
                'active' => (int)$validated[5],
                'region' => sizeof($area) ? $area[0] : null,
                'municipality' => sizeof($municipality) ? $municipality[0] : null,
                'town' => sizeof($settlement) ? $settlement[0] : null,
                'delivery_method' => (int)$validated[10],
                'court_id' => sizeof($court) ? $court[0] : null,
                'redirect_only' => (int)$validated[13],
            ];

            if( $existingItem ) {
                $existingItem->fill($fields);
                $existingItem->translate('bg')->subject_name = $validated[1];
                $existingItem->translate('bg')->address = $validated[9];
                if( !sizeof($court) ) {
                    $existingItem->translate('bg')->court_text = $validated[12];
                }
                $existingItem->save();
                $hasUpdates += 1;
            } else {
                $fields['eik'] = $validated[0];
                $fields['adm_register'] = 0;//manual
                $newItem = PdoiResponseSubject::create($fields);
                if( $newItem ) {
                    $newItem->translateOrNew('bg')->subject_name = $validated[1];
                    $newItem->translateOrNew('bg')->address = $validated[9];
                    if( !sizeof($court) ) {
                        $newItem->translateOrNew('bg')->court_text = $validated[12];
                    }
                    $newItem->save();
                    $hasInserts += 1;
                }
            }
        }
        if( $hasInserts || $hasUpdates ) {
            $message = ($hasInserts ? 'Добавени субекти ('.$hasInserts.')' : '').'<br>'.($hasUpdates ? 'Обновени субекти ('.$hasUpdates.')' : '');
            if( !empty($message) ) {
                session()->flash('success', $message);
            }
        }

        if( !empty($errors) ) {
            $errMsg = '';
            foreach ($errors as $key => $rowErrors) {
                $errMsg .= '<br>Грешки в ред ('.$key.'):<br>';
                foreach ($rowErrors as $err ) {
                    $errMsg .= $err.';';
                }
            }
            if( !empty($errMsg) ) {
                session()->flash('danger', $errMsg);
            }
        }
    }

    private function rules()
    {
        return [
            '0' => ['required', 'digits_between:1,13'],
            '1' => ['required', 'string', 'max:255'],
            '2' => ['nullable', 'email', 'max:255', 'required_if:10,1'],
            '3' => ['required', 'numeric', Rule::in($this->admLevelsRuleIn)],
            '4' => ['nullable', 'digits_between:1,13', Rule::in($this->responseSubjectsRuleIn)],
            '5' => ['required', 'numeric', 'in:0,1'],
            '6' => ['required', 'string', Rule::in($this->areasRuleIn)],
            '7' => ['required', 'string', Rule::in($this->municipalityRuleIn)],
            '8' => ['required', 'numeric', Rule::in($this->settlementRuleIn)],
            '9' => ['required', 'string', 'max:255'],
            '10' => ['required', 'numeric', Rule::in(PdoiSubjectDeliveryMethodsEnum::values())],
            '11' => ['nullable', 'digits_between:1,13', Rule::in($this->responseSubjectsRuleIn)],
            '12' => ['nullable', 'required_without:11', 'string', 'max:255'],
            '13' => ['required', 'numeric', 'in:0,1'],
        ];
    }

    private function attributes(): array
    {
        return [
            '0' => 'ЕИК/Булстат',
            '1' => 'Наименование',
            '2' => 'Ел. поща',
            '3' => 'Административно ниво',
            '4' => 'Подчинен на',
            '5' => 'Статус',
            '6' => 'Област КОД',
            '7' => 'Община КОД',
            '8' => 'Населено място',
            '9' => 'Адрес',
            '10' => 'Начин на пренасочване',
            '11' => 'Компетентен съдебен орган от РЗС (ЕИК)',
            '12' => 'Друг Компетентен съдебен орган(свободен текст)',
            '13' => 'Само пренасоване по компетентност',
        ];
    }
}
