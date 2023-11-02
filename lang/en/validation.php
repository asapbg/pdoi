<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'accepted_if' => 'The :attribute must be accepted when :other is :value.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => 'The :attribute must only contain letters.',
    'alpha_dash' => 'The :attribute must only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The :attribute must only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'array' => 'The :attribute must have between :min and :max items.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'numeric' => 'The :attribute must be between :min and :max.',
        'string' => 'The :attribute must be between :min and :max characters.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'current_password' => 'The password is incorrect.',
    'date' => 'The :attribute is not a valid date.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'declined' => 'The :attribute must be declined.',
    'declined_if' => 'The :attribute must be declined when :other is :value.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => 'The :attribute must be a valid email address.',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'enum' => 'The selected :attribute is invalid.',
    'exists' => 'The selected :attribute is invalid.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'array' => 'The :attribute must have more than :value items.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'numeric' => 'The :attribute must be greater than :value.',
        'string' => 'The :attribute must be greater than :value characters.',
    ],
    'gte' => [
        'array' => 'The :attribute must have :value items or more.',
        'file' => 'The :attribute must be greater than or equal to :value kilobytes.',
        'numeric' => 'The :attribute must be greater than or equal to :value.',
        'string' => 'The :attribute must be greater than or equal to :value characters.',
    ],
    'image' => 'The :attribute must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'array' => 'The :attribute must have less than :value items.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'numeric' => 'The :attribute must be less than :value.',
        'string' => 'The :attribute must be less than :value characters.',
    ],
    'lte' => [
        'array' => 'The :attribute must not have more than :value items.',
        'file' => 'The :attribute must be less than or equal to :value kilobytes.',
        'numeric' => 'The :attribute must be less than or equal to :value.',
        'string' => 'The :attribute must be less than or equal to :value characters.',
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max' => [
        'array' => 'The :attribute must not have more than :max items.',
        'file' => 'The :attribute must not be greater than :max kilobytes.',
        'numeric' => 'The :attribute must not be greater than :max.',
        'string' => 'The :attribute must not be greater than :max characters.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'array' => 'The :attribute must have at least :min items.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'numeric' => 'The :attribute must be at least :min.',
        'string' => 'The :attribute must be at least :min characters.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => 'The :attribute must be a number.',
    'password' => 'The password is incorrect.',
    'present' => 'The :attribute field must be present.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => 'The :attribute format is invalid.',
    'required' => 'The :attribute field is required.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'array' => 'The :attribute must contain :size items.',
        'file' => 'The :attribute must be :size kilobytes.',
        'numeric' => 'The :attribute must be :size.',
        'string' => 'The :attribute must be :size characters.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid timezone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute must be a valid URL.',
    'uuid' => 'The :attribute must be a valid UUID.',

    'alpha_space' => ':attribute must contain only letters and a space',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'roles' => [
            'required' => 'You must select at least one role',
        ],
        'sector_roles.*.storage_location_id' => [
            'required_with' => 'Когато сте избрали сектор трябва да изберете и склад',
        ],
        'password' => [
            'Illuminate\Validation\Rules\Password' => 'The password must contain',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'analysis_types_text'      => 'Вид анализ',
        'complexity'               => 'Комплексност',
        'roles'                    => 'Role',
        'groups'                   => 'Group',
        'status'                   => 'Status',
        'section'                  => 'Section',
        'alias'                    => 'username',
        'name'                     => 'name',
        'name_bg'                  => 'Name (BG)',
        'name_en'                  => 'Name (EN)',
        'display_name'             => 'Name',
        'username'                 => 'Username',
        'email'                    => 'Email',
        'first_name'               => 'First name',
        'middle_name'              => 'Middle name',
        'last_name'                => 'Last name',
        'victim_egn'               => 'ID',
        'password'                 => 'Password',
        'password_confirm'         => 'Confirm password',
        'password_confirmation'    => 'Confirm password',
        'must_change_password'     => 'Send an automatic email to the user with a link to enter their password themselves',
        'investigation_number'     => 'Correspondence #',
        'investigation_year'       => 'Correspondence year',
        'assignor_number'          => 'Възложител писмо №',
        'assignor_date'            => 'Възложител писмо дата',
        'city_id'                  => 'Town',
        'court_id'                 => 'Court',
        'police_office_id'         => 'Ministry of Internal Affairs',
        'prosecutor_office_id'     => 'Prosecutor\'s office',
        'other_id'                 => 'Друг Възложител',
        'city'                     => 'Town',
        'country'                  => 'Country',
        'address'                  => 'Address',
        'phone'                    => 'Phone',
        'channels'                 => 'Комуникационни ресурси',
        'age'                      => 'Age',
        'sex'                      => 'Sex',
        'gender'                   => 'Gender',
        'day'                      => 'Day',
        'month'                    => 'Month',
        'year'                     => 'Year',
        'hour'                     => 'Hour',
        'minute'                   => 'Minute',
        'second'                   => 'Second',
        'title'                    => 'Title',
        'content'                  => 'Content',
        'description'              => 'Description',
        'excerpt'                  => 'An excerpt',
        'date'                     => 'Date',
        'time'                     => 'Time',
        'available'                => 'Available',
        'size'                     => 'Size',
        'recaptcha_response_field' => 'Recaptcha',
        'subject'                  => 'Subject',
        'message'                  => 'Message',
        'rememberme'               => 'Remember me',
        'role'                     => 'Role',
        'created_at'               => 'Created at',
        'updated_at'               => 'Last update',
        'is_approved'              => 'approved',
        'type'                     => 'Type',
        'visibility'               => 'Visibility',
        'image'                    => 'Image',
        'notes'                    => 'Notes',
        'color'                     => 'Color',
        'number'                    => 'Number',
        'count'                     => 'Count',
        'start_time'                => 'Start time',
        'text'                      => 'Text',
        'begin_date'                => 'start date',
        'model'                     => 'Model',

        //
        'permission_group'          => 'Нова група права',
        'names'                     => 'Names',
        'user_type'                 => 'User type',
        'administrative_unit'       => 'Administrative unit',
        'lang'                      => 'Language for work',
        'eik'                       => 'UIC',
        'subject_name'              => 'Name',
        'subject_name_bg'           => 'Name (BG)',
        'subject_name_en'           => 'Name (EN)',
        'adm_level'                 => 'Institution level',
        'parent_id'                    => 'Level',
        'fax'                       => 'Fax',
        'add_info'                  => 'Additional info',
        'date_from'                 => 'Date (from)',
        'date_to'                   => 'Date (to)',
        'area'                      => 'Area',
        'municipality'              => 'Municipality',
        'settlement'                => 'Settlement',
        'zip_code'                  => 'Zip code',
        'redirect_only'             => 'Forwarded by competency',
        'oblast_code'               => 'Area (code)',
        'ekatte'                    => 'ЕКАТТЕ',
        'region'                    => 'Region',
        'ekkate_document'           => 'Document code',
        'abc'                       => 'ABC',
        'ime'                      => 'Name',
        'obstina_code'              => 'Municipality (code)',
        'obstina'                   => 'Municipality',
        'category'                  => 'Category',
        'tmv'                       => 'TMV',
        'kmetstvo'                  => 'City Hall',
        'kind'                      => 'Type',
        'altitude'                  => 'Надморска височина',
        'tsb'                       => 'ТСБ',
        'manual_rzs'                => 'Manually registered',
        'user_legal_form'           => 'Legal form',
        'legal_form'                => 'Legal form',
        'profile_type'              => 'Profile type',
        'person_identity'           => 'Person identity',
        'company_identity'          => 'Company identity',
        'post_code'                 => 'Post code',
        'address_second'            => 'Address (second)',
        'delivery_method'            => 'Method of obtaining the requested public information',
        'rzs_delivery_method'=> 'Forwarding method',
        'court_text'                => 'Judiciary (text)',
        'court_text_bg'                => 'Judiciary (text) (BG)',
        'court_text_en'                => 'Judiciary (text) (EN)',
        'court'                     => 'Judiciary (Iisda)',
        'add_text'                  => 'Additional information',
        'decision'                  => 'Decision',
        'file_decision'             => 'File (judgment)',
        'full_names'                => 'Full name',
        'applicant_type'            => 'Profile type',
        'country_id'                => 'Country',
        'request'                   => 'Request',
        'response_subject_id'       => 'Institution',
        'order_idx'                 => 'Order',
        'slug'                      => 'Slug',
        'meta_title'                => 'Title (meta)',
        'meta_description'          => 'Description (meta)',
        'meta_keyword'              => 'Keywords (meta)',
        'short_content'              => 'Short description',
        'files.*'                   => 'File',
        'file_description.*'        => 'File description',
        'file'                      => 'File',
        'response'                  => 'Response',
        'address_bg'                => 'Address (BG)',
        'address_en'                => 'Address (EN)',
        'extra_info'                => 'Additional information',
    ],

];
