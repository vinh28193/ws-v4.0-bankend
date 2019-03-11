<?php

use yii\db\Migration;

class m180628_144130_create_table_country extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%country}}', [
            'code' => $this->string()->notNull()->append('PRIMARY KEY'),
            'name' => $this->string()->notNull(),
                ], $tableOptions);

        $this->createIndex('country_code_idx', '{{%country}}', 'code', true);

        $transaction = $this->db->beginTransaction();
        try {
            $this->batchInsert('{{%country}}', ['code', 'name'], [
                ['TW', "Taiwan"],
                ['AF', "Afghanistan"],
                ['AL', "Albania"],
                ['DZ', "Algeria"],
                ['AS', "American Samoa"],
                ['AD', "Andorra"],
                ['AO', "Angola"],
                ['AI', "Anguilla"],
                ['AQ', "Antarctica"],
                ['AG', "Antigua and Barbuda"],
                ['AR', "Argentina"],
                ['AM', "Armenia"],
                ['AW', "Aruba"],
                ['AU', "Australia"],
                ['AT', "Austria"],
                ['AZ', "Azerbaijan"],
                ['BS', "Bahamas"],
                ['BH', "Bahrain"],
                ['BD', "Bangladesh"],
                ['BB', "Barbados"],
                ['BY', "Belarus"],
                ['BE', "Belgium"],
                ['BZ', "Belize"],
                ['BJ', "Benin"],
                ['BM', "Bermuda"],
                ['BT', "Bhutan"],
                ['BO', "Bolivia (Plurinational State of)"],
                ['BQ', "Bonaire - Sint Eustatius and Saba"],
                ['BA', "Bosnia and Herzegovina"],
                ['BW', "Botswana"],
                ['BV', "Bouvet Island"],
                ['BR', "Brazil"],
                ['IO', "British Indian Ocean Territory"],
                ['VG', "British Virgin Islands"],
                ['BN', "Brunei Darussalam"],
                ['BG', "Bulgaria"],
                ['BF', "Burkina Faso"],
                ['BI', "Burundi"],
                ['CV', "Cabo Verde"],
                ['KH', "Cambodia"],
                ['CM', "Cameroon"],
                ['CA', "Canada"],
                ['KY', "Cayman Islands"],
                ['CF', "Central African Republic"],
                ['TD', "Chad"],
                ['CL', "Chile"],
                ['CN', "China"],
                ['HK', "China - Hong Kong Special Administrative Region"],
                ['MO', "China - Macao Special Administrative Region"],
                ['CX', "Christmas Island"],
                ['CC', "Cocos (Keeling) Islands"],
                ['CO', "Colombia"],
                ['KM', "Comoros"],
                ['CG', "Congo"],
                ['CK', "Cook Islands"],
                ['CR', "Costa Rica"],
                ['HR', "Croatia"],
                ['CU', "Cuba"],
                ['CW', "Curaçao"],
                ['CY', "Cyprus"],
                ['CZ', "Czechia"],
                ['CI', "Côte d'Ivoire"],
                ['KP', "Democratic People's Republic of Korea"],
                ['CD', "Democratic Republic of the Congo"],
                ['DK', "Denmark"],
                ['DJ', "Djibouti"],
                ['DM', "Dominica"],
                ['DO', "Dominican Republic"],
                ['EC', "Ecuador"],
                ['EG', "Egypt"],
                ['SV', "El Salvador"],
                ['GQ', "Equatorial Guinea"],
                ['ER', "Eritrea"],
                ['EE', "Estonia"],
                ['ET', "Ethiopia"],
                ['FK', "Falkland Islands (Malvinas)"],
                ['FO', "Faroe Islands"],
                ['FJ', "Fiji"],
                ['FI', "Finland"],
                ['FR', "France"],
                ['GF', "French Guiana"],
                ['PF', "French Polynesia"],
                ['TF', "French Southern Territories"],
                ['GA', "Gabon"],
                ['GM', "Gambia"],
                ['GE', "Georgia"],
                ['DE', "Germany"],
                ['GH', "Ghana"],
                ['GI', "Gibraltar"],
                ['GR', "Greece"],
                ['GL', "Greenland"],
                ['GD', "Grenada"],
                ['GP', "Guadeloupe"],
                ['GU', "Guam"],
                ['GT', "Guatemala"],
                ['GG', "Guernsey"],
                ['GN', "Guinea"],
                ['GW', "Guinea-Bissau"],
                ['GY', "Guyana"],
                ['HT', "Haiti"],
                ['HM', "Heard Island and McDonald Islands"],
                ['VA', "Holy See"],
                ['HN', "Honduras"],
                ['HU', "Hungary"],
                ['IS', "Iceland"],
                ['IN', "India"],
                ['ID', "Indonesia"],
                ['IR', "Iran (Islamic Republic of)"],
                ['IQ', "Iraq"],
                ['IE', "Ireland"],
                ['IM', "Isle of Man"],
                ['IL', "Israel"],
                ['IT', "Italy"],
                ['JM', "Jamaica"],
                ['JP', "Japan"],
                ['JE', "Jersey"],
                ['JO', "Jordan"],
                ['KZ', "Kazakhstan"],
                ['KE', "Kenya"],
                ['KI', "Kiribati"],
                ['KW', "Kuwait"],
                ['KG', "Kyrgyzstan"],
                ['LA', "Lao People's Democratic Republic"],
                ['LV', "Latvia"],
                ['LB', "Lebanon"],
                ['LS', "Lesotho"],
                ['LR', "Liberia"],
                ['LY', "Libya"],
                ['LI', "Liechtenstein"],
                ['LT', "Lithuania"],
                ['LU', "Luxembourg"],
                ['MG', "Madagascar"],
                ['MW', "Malawi"],
                ['MY', "Malaysia"],
                ['MV', "Maldives"],
                ['ML', "Mali"],
                ['MT', "Malta"],
                ['MH', "Marshall Islands"],
                ['MQ', "Martinique"],
                ['MR', "Mauritania"],
                ['MU', "Mauritius"],
                ['YT', "Mayotte"],
                ['MX', "Mexico"],
                ['FM', "Micronesia (Federated States of)"],
                ['MC', "Monaco"],
                ['MN', "Mongolia"],
                ['ME', "Montenegro"],
                ['MS', "Montserrat"],
                ['MA', "Morocco"],
                ['MZ', "Mozambique"],
                ['MM', "Myanmar"],
                ['NA', "Namibia"],
                ['NR', "Nauru"],
                ['NP', "Nepal"],
                ['NL', "Netherlands"],
                ['NC', "New Caledonia"],
                ['NZ', "New Zealand"],
                ['NI', "Nicaragua"],
                ['NE', "Niger"],
                ['NG', "Nigeria"],
                ['NU', "Niue"],
                ['NF', "Norfolk Island"],
                ['MP', "Northern Mariana Islands"],
                ['NO', "Norway"],
                ['OM', "Oman"],
                ['PK', "Pakistan"],
                ['PW', "Palau"],
                ['PA', "Panama"],
                ['PG', "Papua New Guinea"],
                ['PY', "Paraguay"],
                ['PE', "Peru"],
                ['PH', "Philippines"],
                ['PN', "Pitcairn"],
                ['PL', "Poland"],
                ['PT', "Portugal"],
                ['PR', "Puerto Rico"],
                ['QA', "Qatar"],
                ['KR', "Republic of Korea"],
                ['MD', "Republic of Moldova"],
                ['RO', "Romania"],
                ['RU', "Russian Federation"],
                ['RW', "Rwanda"],
                ['RE', "Réunion"],
                ['BL', "Saint Barthélemy"],
                ['SH', "Saint Helena"],
                ['KN', "Saint Kitts and Nevis"],
                ['LC', "Saint Lucia"],
                ['MF', "Saint Martin (French Part)"],
                ['PM', "Saint Pierre and Miquelon"],
                ['VC', "Saint Vincent and the Grenadines"],
                ['WS', "Samoa"],
                ['SM', "San Marino"],
                ['ST', "Sao Tome and Principe"],
                ['SA', "Saudi Arabia"],
                ['SN', "Senegal"],
                ['RS', "Serbia"],
                ['SC', "Seychelles"],
                ['SL', "Sierra Leone"],
                ['SG', "Singapore"],
                ['SX', "Sint Maarten (Dutch part)"],
                ['SK', "Slovakia"],
                ['SI', "Slovenia"],
                ['SB', "Solomon Islands"],
                ['SO', "Somalia"],
                ['ZA', "South Africa"],
                ['GS', "South Georgia and the South Sandwich Islands"],
                ['SS', "South Sudan"],
                ['ES', "Spain"],
                ['LK', "Sri Lanka"],
                ['PS', "State of Palestine"],
                ['SD', "Sudan"],
                ['SR', "Suriname"],
                ['SJ', "Svalbard and Jan Mayen Islands"],
                ['SZ', "Swaziland"],
                ['SE', "Sweden"],
                ['CH', "Switzerland"],
                ['SY', "Syrian Arab Republic"],
                ['TJ', "Tajikistan"],
                ['TH', "Thailand"],
                ['MK', "The former Yugoslav Republic of Macedonia"],
                ['TL', "Timor-Leste"],
                ['TG', "Togo"],
                ['TK', "Tokelau"],
                ['TO', "Tonga"],
                ['TT', "Trinidad and Tobago"],
                ['TN', "Tunisia"],
                ['TR', "Turkey"],
                ['TM', "Turkmenistan"],
                ['TC', "Turks and Caicos Islands"],
                ['TV', "Tuvalu"],
                ['UG', "Uganda"],
                ['UA', "Ukraine"],
                ['AE', "United Arab Emirates"],
                ['GB', "United Kingdom of Great Britain and Northern Ireland"],
                ['TZ', "United Republic of Tanzania"],
                ['UM', "United States Minor Outlying Islands"],
                ['VI', "United States Virgin Islands"],
                ['UY', "Uruguay"],
                ['UZ', "Uzbekistan"],
                ['VU', "Vanuatu"],
                ['VE', "Venezuela (Bolivarian Republic of)"],
                ['VN', "Viet Nam"],
                ['WF', "Wallis and Futuna Islands"],
                ['EH', "Western Sahara"],
                ['YE', "Yemen"],
                ['ZM', "Zambia"],
                ['ZW', "Zimbabwe"],
                ['AX', "Åland Islands"],
                ['US', "USA"],
            ]);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            echo "Error importing country data!\n";
            echo $e->getMessage() . ' at ' . $e->getLine() . ' in ' . $e->getFile() . PHP_EOL;
            echo $e->getTraceAsString() . PHP_EOL;
        }
    }

    public function down() {
        $this->dropTable('{{%country}}');
    }

}
