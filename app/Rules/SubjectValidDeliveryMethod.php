<?php

namespace App\Rules;

use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Models\Egov\EgovOrganisation;
use App\Models\PdoiResponseSubject;
use Illuminate\Contracts\Validation\Rule;

class SubjectValidDeliveryMethod implements Rule
{
    private int $id;
    private int $fullEdit;
    private string|null $eik;
    private string|null $email;
    private string $message;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id, $fullEdit, $eik, $email)
    {
        $this->id = $id;
        $this->fullEdit = $fullEdit;
        $this->eik = $eik;
        $this->email = $email;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if( (int)$value == PdoiSubjectDeliveryMethodsEnum::EMAIL->value ) {
            if( $this->fullEdit ) {
                if( empty($this->email) ) {
                    $this->message = 'Методът на пренасочване исиква валиден ел. адрес за задълженият субкет.';
                    return false;
                }
            } else{
                $subject = PdoiResponseSubject::whereNotNull('email')->where('email', '<>', '')->find($this->id);
                if( !$subject ) {
                    $this->message = 'Методът на пренасочване исиква валиден ел. адрес за задълженият субкет.';
                    return false;
                }
            }
        }
        if( (int)$value == PdoiSubjectDeliveryMethodsEnum::SEOS->value ) {
            if( $this->fullEdit ) {
                if( empty($this->eik) ) {
                    $this->message = 'Методът на пренасочване исиква валиден EIK за задълженият субкет.';
                    return false;
                }
                $subjectEik = $this->eik;
            } else {
                $subject = PdoiResponseSubject::whereNotNull('eik')->where('eik', '<>', '')->where('active', 1)->find($this->id);
                if( !$subject ) {
                    $this->message = 'Методът на пренасочване исиква валиден EIK за задълженият субкет.';
                    return false;
                }
                $subjectEik = $subject->eik;
            }

            $organisation = EgovOrganisation::where('eik', '=', $subjectEik)->where('status', 1)->first();
            if( !$organisation ) {
                $this->message = 'Методът на пренасочване исиква съществуването на организация/участник в СЕОС регистъра с този ЕИК';
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
