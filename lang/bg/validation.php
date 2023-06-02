<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages.
    |
    */

    'username_exists' => 'Потребител с потребителско име :Username вече съществува!',
    'email_exists'    => 'Потребител с email :email вече съществува!',
    'accepted'        => 'Трябва да приемете :attribute.',
    'active_url'      => 'Полето :attribute не е валиден URL адрес.',
    'after'           => 'Полето :attribute трябва да бъде дата след :date.',
    'after_or_equal'  => 'Полето :attribute трябва да бъде дата след или равна на :date.',
    'alpha'           => 'Полето :attribute трябва да съдържа само букви.',
    'alpha_dash'      => 'Полето :attribute трябва да съдържа само букви, цифри, долна черта и тире.',
    'alpha_num'       => 'Полето :attribute трябва да съдържа само букви и цифри.',
    'array'           => 'Полето :attribute трябва да бъде масив.',
    'before'          => 'Полето :attribute трябва да бъде дата преди :date.',
    'before_or_equal' => 'Полето :attribute трябва да бъде дата преди или равна на :date.',
    'between'         => [
        'numeric' => 'Полето :attribute трябва да бъде между :min и :max.',
        'file'    => 'Полето :attribute трябва да бъде между :min и :max килобайта.',
        'string'  => 'Полето :attribute трябва да бъде между :min и :max знака.',
        'array'   => 'Полето :attribute трябва да има между :min - :max елемента.',
    ],
    'boolean'        => 'Полето :attribute трябва да съдържа Да или Не',
    'confirmed'      => 'Полето :attribute не е потвърдено.',
    'date'           => 'Полето :attribute не е валидна дата.',
    'date_equals'    => ':attribute трябва да бъде дата, еднаква с :date.',
    'date_format'    => 'Полето :attribute не е във формат :format.',
    'different'      => 'Полетата :attribute и :other трябва да са различни.',
    'digits'         => 'Полето :attribute трябва да има :digits цифри.',
    'digits_between' => 'Полето :attribute трябва да има между :min и :max цифри.',
    'dimensions'     => 'Невалидни размери за снимка :attribute.',
    'distinct'       => 'Данните в полето :attribute се дублират.',
    'email'          => 'Полето :attribute е в невалиден формат.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'exists'         => 'Избранато поле :attribute вече съществува.',
    'file'           => 'Полето :attribute трябва да бъде файл.',
    'filled'         => 'Полето :attribute е задължително.',
    'gt'             => [
        'numeric' => ':attribute трябва да бъде по-голямa от :value.',
        'file'    => ':attribute трябва да бъде по-голяма от :valueкилобайта.',
        'string'  => ':attribute трябва да бъде по-голяма от :value знака.',
        'array'   => ':attribute трябва да разполага с повече от :value елемента.',
    ],
    'gte' => [
        'numeric' => ':attribute трябва да бъде по-голяма от или равна на :value.',
        'file'    => ':attribute трябва да бъде по-голяма от или равна на :value килобайта.',
        'string'  => ':attribute трябва да бъде по-голяма от или равна на :valueзнака.',
        'array'   => ':attribute трябва да разполага с :value елемента или повече.',
    ],
    'image'    => 'Полето :attribute трябва да бъде изображение.',
    'in'       => 'Избраното поле :attribute е невалидно.',
    'in_array' => 'Полето :attribute не съществува в :other.',
    'integer'  => 'Полето :attribute трябва да бъде цяло число.',
    'ip'       => 'Полето :attribute трябва да бъде IP адрес.',
    'ipv4'     => 'Полето :attribute трябва да бъде IPv4 адрес.',
    'ipv6'     => 'Полето :attribute трябва да бъде IPv6 адрес.',
    'json'     => 'Полето :attribute трябва да бъде JSON низ.',
    'lt'       => [
        'numeric' => ':attribute трябва да бъде по-малка от :value.',
        'file'    => ':attribute трябва да бъде по-малка от :value килобайта.',
        'string'  => ':attribute трябва да бъде по-малка от :value знака.',
        'array'   => ':attribute трябва да разполага с по-малко от :value елемента.',
    ],
    'lte' => [
        'numeric' => ':attribute трябва да бъде по-малка от или равна на :value.',
        'file'    => ':attribute трябва да бъде по-малка от или равна на :value килобайта.',
        'string'  => ':attribute трябва да бъде по-малка от или равна на :value знака.',
        'array'   => ':attribute не трябва да разполага с повече от :value елемента.',
    ],
    'max' => [
        'numeric' => 'Полето :attribute трябва да бъде по-малко от :max.',
        'file'    => 'Полето :attribute трябва да бъде по-малко от :max килобайта.',
        'string'  => 'Полето :attribute трябва да бъде по-малко от :max знака.',
        'array'   => 'Полето :attribute трябва да има по-малко от :max елемента.',
    ],
    'mimes'     => 'Полето :attribute трябва да бъде файл от тип: :values.',
    'mimetypes' => 'Полето :attribute трябва да бъде файл от тип: :values.',
    'min'       => [
        'numeric' => 'Полето :attribute трябва да бъде минимум :min.',
        'file'    => 'Полето :attribute трябва да бъде минимум :min килобайта.',
        'string'  => 'Полето :attribute трябва да бъде минимум :min знака.',
        'array'   => 'Полето :attribute трябва има минимум :min елемента.',
    ],
    'not_in'               => 'Избраното поле :attribute е невалидно.',
    'not_regex'            => 'Форматът на :attribute е невалиден.',
    'numeric'              => 'Полето :attribute трябва да бъде число.',
    'present'              => 'Полето :attribute трябва да съествува.',
    'regex'                => 'Полето :attribute е в невалиден формат.',
    'required'             => 'Полето :attribute е задължително.',
    'required_if'          => 'Полето :attribute се изисква, когато :other е :value.',
    'required_unless'      => 'Полето :attribute се изисква, освен ако :other не е в :values.',
    'required_with'        => 'Полето :attribute се изисква, когато :values има стойност.',
    'required_with_all'    => 'Полето :attribute е задължително, когато :values имат стойност.',
    'required_without'     => 'Полето :attribute се изисква, когато :values няма стойност.',
    'required_without_all' => 'Полето :attribute се изисква, когато никое от полетата :values няма стойност.',
    'same'                 => 'Полетата :attribute и :other трябва да съвпадат.',
    'size'                 => [
        'numeric' => 'Полето :attribute трябва да бъде :size.',
        'file'    => 'Полето :attribute трябва да бъде :size килобайта.',
        'string'  => 'Полето :attribute трябва да бъде :size знака.',
        'array'   => 'Полето :attribute трябва да има :size елемента.',
    ],
    'starts_with' => ':attribute трябва да започва с едно от следните: :values.',
    'string'      => 'Полето :attribute трябва да бъде знаков низ.',
    'timezone'    => 'Полето :attribute трябва да съдържа валидна часова зона.',
    'unique'      => 'Полето :attribute вече съществува.',
    'uploaded'    => 'Неуспешно качване на :attribute.',
    'url'         => 'Полето :attribute е в невалиден формат.',
    'uuid'        => ':attribute трябва да бъде валиден UUID.',

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
            'required' => 'Трябва да изберете поне една роля',
        ],
        'sector_roles.*.storage_location_id' => [
            'required_with' => 'Когато сте избрали сектор трябва да изберете и склад',
        ],
        'password' => [
            'Illuminate\Validation\Rules\Password' => 'Паролата трябва да съдържа',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'analysis_types_text'      => 'Вид анализ',
        'complexity'               => 'Комплексност',
        'roles'                    => 'Роля',
        'groups'                   => 'Група',
        'status'                   => 'Статус',
        'alias'                    => 'Псевдоним',
        'name'                     => 'Наименование',
        'display_name'             => 'Наименование',
        'username'                 => 'Потребителско име',
        'email'                    => 'Електр.поща',
        'first_name'               => 'Име',
        'middle_name'              => 'Презиме',
        'last_name'                => 'Фамилия',
        'victim_egn'               => 'ЕГН',
        'password'                 => 'Парола',
        'password_confirm'         => 'Потвърди Парола',
        'password_confirmation'    => 'Потвърди Парола',
        'must_change_password'     => 'Да се изпрати автоматичен email до потребителя с линк за да въведе паролата си сам',
        'investigation_number'     => 'Преписка №',
        'investigation_year'       => 'Преписка година',
        'assignor_number'          => 'Възложител писмо №',
        'assignor_date'            => 'Възложител писмо дата',
        'city_id'                  => 'Град',
        'court_id'                 => 'Съд',
        'police_office_id'         => 'МВР',
        'prosecutor_office_id'     => 'Прокуратура',
        'other_id'                 => 'Друг Възложител',
        'city'                     => 'Град',
        'country'                  => 'Държава',
        'address'                  => 'Адрес',
        'phone'                    => 'Телефон',
        'channels'                 => 'Комуникационни ресурси',
        'age'                      => 'Възраст',
        'sex'                      => 'Пол',
        'gender'                   => 'Пол',
        'day'                      => 'Ден',
        'month'                    => 'Месец',
        'year'                     => 'Година',
        'hour'                     => 'Час',
        'minute'                   => 'Минута',
        'second'                   => 'Секунда',
        'title'                    => 'Заглавие',
        'content'                  => 'Съдържание',
        'description'              => 'Описание',
        'excerpt'                  => 'Откъс',
        'date'                     => 'Дата',
        'time'                     => 'Време',
        'available'                => 'Достъпен',
        'size'                     => 'Размер',
        'recaptcha_response_field' => 'Рекапча',
        'subject'                  => 'Заглавие',
        'message'                  => 'Съобщение',
        'rememberme'               => 'Запомни ме',
        'role'                     => 'Роля',
        'created_at'               => 'Дата на създаване',
        'updated_at'               => 'Последна промяна',
        'is_approved'              => 'Одобрен',
        'type'                     => 'Тип',
        'visibility'               => 'Видимост',
        'image'                    => 'Изображение',
        'notes'                    => 'Бележки',
        'color'                     => 'Цвят',
        'number'                    => 'Номер',
        'count'                     => 'Брой',
        'start_time'                => 'Начално време',
        'text'                      => 'Текст',
        'begin_date'                => 'дата на започване',
        'model'                     => 'Модел',

        //
        'permission_group'          => 'Нова група права',
        'names'                     => 'Имена',
        'user_type'                 => 'Тип потребител',
        'administrative_unit'       => 'Адм. звено',
        'lang'                      => 'Език за работа',
        'eik'                       => 'ЕИК',
        'subject_name'              => 'Наименование',
        'adm_level'                 => 'Ниво',
        'fax'                       => 'Факс',
        'add_info'                  => 'Допъл. информация',
        'date_from'                 => 'Дата от',
        'date_to'                   => 'Дата до',
        'area'                      => 'Област',
        'municipality'              => 'Община',
        'settlement'                => 'Населено място',
        'zip_code'                  => 'Пощенски код',
        'redirect_only'             => 'Пренасочване по компетентност',
        'oblast_code'               => 'Област (код)',
        'ekatte'                    => 'ЕКАТТЕ',
        'region'                    => 'Регион',
        'ekkate_document'           => 'Код на документа',
        'abc'                       => 'ABC',
        'ime'                      => 'Наименование',
        'obstina_code'              => 'Община (код)',
        'obstina'                   => 'Община',
        'category'                  => 'Категория',
        'tmv'                       => 'TMV',
        'kmetstvo'                  => 'Кметство',
        'kind'                      => 'Вид',
        'altitude'                  => 'Надморска височина',
        'tsb'                       => 'ТСБ',
    ],
];
