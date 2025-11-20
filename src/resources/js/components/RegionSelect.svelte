<script lang="ts">
  import { Input } from 'flowbite-svelte';

  interface Region {
    code: string;
    name: string;
  }

  interface Props {
    value: string;
  }

  let { value = $bindable() }: Props = $props();

  // https://developers.google.com/maps/coverage.md.txt
  const regions: Region[] = [
    { code: 'AC', name: 'Ascension Island' },
    { code: 'AD', name: 'Andorra' },
    { code: 'AE', name: 'United Arab Emirates' },
    { code: 'AF', name: 'Afghanistan' },
    { code: 'AG', name: 'Antigua & Barbuda' },
    { code: 'AI', name: 'Anguilla' },
    { code: 'AL', name: 'Albania' },
    { code: 'AM', name: 'Armenia' },
    { code: 'AO', name: 'Angola' },
    { code: 'AQ', name: 'Antarctica' },
    { code: 'AR', name: 'Argentina' },
    { code: 'AS', name: 'American Samoa' },
    { code: 'AT', name: 'Austria' },
    { code: 'AU', name: 'Australia' },
    { code: 'AW', name: 'Aruba' },
    { code: 'AX', name: 'Åland Islands' },
    { code: 'AZ', name: 'Azerbaijan' },
    { code: 'BA', name: 'Bosnia & Herzegovina' },
    { code: 'BB', name: 'Barbados' },
    { code: 'BD', name: 'Bangladesh' },
    { code: 'BE', name: 'Belgium' },
    { code: 'BF', name: 'Burkina Faso' },
    { code: 'BG', name: 'Bulgaria' },
    { code: 'BH', name: 'Bahrain' },
    { code: 'BI', name: 'Burundi' },
    { code: 'BJ', name: 'Benin' },
    { code: 'BL', name: 'St. Barthélemy' },
    { code: 'BM', name: 'Bermuda' },
    { code: 'BN', name: 'Brunei' },
    { code: 'BO', name: 'Bolivia' },
    { code: 'BQ', name: 'Caribbean Netherlands' },
    { code: 'BR', name: 'Brazil' },
    { code: 'BS', name: 'Bahamas' },
    { code: 'BT', name: 'Bhutan' },
    { code: 'BV', name: 'Bouvet Island' },
    { code: 'BW', name: 'Botswana' },
    { code: 'BY', name: 'Belarus' },
    { code: 'BZ', name: 'Belize' },
    { code: 'CA', name: 'Canada' },
    { code: 'CC', name: 'Cocos (Keeling) Islands' },
    { code: 'CD', name: 'Congo - Kinshasa' },
    { code: 'CF', name: 'Central African Republic' },
    { code: 'CG', name: 'Congo - Brazzaville' },
    { code: 'CH', name: 'Switzerland' },
    { code: 'CI', name: "Côte d'Ivoire" },
    { code: 'CK', name: 'Cook Islands' },
    { code: 'CL', name: 'Chile' },
    { code: 'CM', name: 'Cameroon' },
    { code: 'CN', name: 'China' },
    { code: 'CO', name: 'Colombia' },
    { code: 'CP', name: 'Clipperton Island' },
    { code: 'CR', name: 'Costa Rica' },
    { code: 'CU', name: 'Cuba' },
    { code: 'CV', name: 'Cape Verde' },
    { code: 'CW', name: 'Curaçao' },
    { code: 'CX', name: 'Christmas Island' },
    { code: 'CY', name: 'Cyprus' },
    { code: 'CZ', name: 'Czechia' },
    { code: 'DE', name: 'Germany' },
    { code: 'DG', name: 'Diego Garcia' },
    { code: 'DJ', name: 'Djibouti' },
    { code: 'DK', name: 'Denmark' },
    { code: 'DM', name: 'Dominica' },
    { code: 'DO', name: 'Dominican Republic' },
    { code: 'DZ', name: 'Algeria' },
    { code: 'EA', name: 'Ceuta & Melilla' },
    { code: 'EC', name: 'Ecuador' },
    { code: 'EE', name: 'Estonia' },
    { code: 'EG', name: 'Egypt' },
    { code: 'EH', name: 'Western Sahara' },
    { code: 'ER', name: 'Eritrea' },
    { code: 'ES', name: 'Spain' },
    { code: 'ET', name: 'Ethiopia' },
    { code: 'FI', name: 'Finland' },
    { code: 'FJ', name: 'Fiji' },
    { code: 'FK', name: 'Falkland Islands (Islas Malvinas)' },
    { code: 'FM', name: 'Micronesia' },
    { code: 'FO', name: 'Faroe Islands' },
    { code: 'FR', name: 'France' },
    { code: 'GA', name: 'Gabon' },
    { code: 'GB', name: 'United Kingdom' },
    { code: 'GD', name: 'Grenada' },
    { code: 'GE', name: 'Georgia' },
    { code: 'GF', name: 'French Guiana' },
    { code: 'GG', name: 'Guernsey' },
    { code: 'GH', name: 'Ghana' },
    { code: 'GI', name: 'Gibraltar' },
    { code: 'GL', name: 'Greenland' },
    { code: 'GM', name: 'Gambia' },
    { code: 'GN', name: 'Guinea' },
    { code: 'GP', name: 'Guadeloupe' },
    { code: 'GQ', name: 'Equatorial Guinea' },
    { code: 'GR', name: 'Greece' },
    { code: 'GS', name: 'South Georgia & South Sandwich Islands' },
    { code: 'GT', name: 'Guatemala' },
    { code: 'GU', name: 'Guam' },
    { code: 'GW', name: 'Guinea-Bissau' },
    { code: 'GY', name: 'Guyana' },
    { code: 'HK', name: 'Hong Kong' },
    { code: 'HM', name: 'Heard & McDonald Islands' },
    { code: 'HN', name: 'Honduras' },
    { code: 'HR', name: 'Croatia' },
    { code: 'HT', name: 'Haiti' },
    { code: 'HU', name: 'Hungary' },
    { code: 'IC', name: 'Canary Islands' },
    { code: 'ID', name: 'Indonesia' },
    { code: 'IE', name: 'Ireland' },
    { code: 'IL', name: 'Israel' },
    { code: 'IM', name: 'Isle of Man' },
    { code: 'IN', name: 'India' },
    { code: 'IO', name: 'British Indian Ocean Territory' },
    { code: 'IQ', name: 'Iraq' },
    { code: 'IR', name: 'Iran' },
    { code: 'IS', name: 'Iceland' },
    { code: 'IT', name: 'Italy' },
    { code: 'JE', name: 'Jersey' },
    { code: 'JM', name: 'Jamaica' },
    { code: 'JO', name: 'Jordan' },
    { code: 'JP', name: 'Japan' },
    { code: 'KE', name: 'Kenya' },
    { code: 'KG', name: 'Kyrgyzstan' },
    { code: 'KH', name: 'Cambodia' },
    { code: 'KI', name: 'Kiribati' },
    { code: 'KM', name: 'Comoros' },
    { code: 'KN', name: 'St. Kitts & Nevis' },
    { code: 'KP', name: 'North Korea' },
    { code: 'KR', name: 'South Korea' },
    { code: 'KW', name: 'Kuwait' },
    { code: 'KY', name: 'Cayman Islands' },
    { code: 'KZ', name: 'Kazakhstan' },
    { code: 'LA', name: 'Laos' },
    { code: 'LB', name: 'Lebanon' },
    { code: 'LC', name: 'St. Lucia' },
    { code: 'LI', name: 'Liechtenstein' },
    { code: 'LK', name: 'Sri Lanka' },
    { code: 'LR', name: 'Liberia' },
    { code: 'LS', name: 'Lesotho' },
    { code: 'LT', name: 'Lithuania' },
    { code: 'LU', name: 'Luxembourg' },
    { code: 'LV', name: 'Latvia' },
    { code: 'LY', name: 'Libya' },
    { code: 'MA', name: 'Morocco' },
    { code: 'MC', name: 'Monaco' },
    { code: 'MD', name: 'Moldova' },
    { code: 'ME', name: 'Montenegro' },
    { code: 'MF', name: 'St. Martin' },
    { code: 'MG', name: 'Madagascar' },
    { code: 'MH', name: 'Marshall Islands' },
    { code: 'MK', name: 'North Macedonia' },
    { code: 'ML', name: 'Mali' },
    { code: 'MM', name: 'Myanmar (Burma)' },
    { code: 'MN', name: 'Mongolia' },
    { code: 'MO', name: 'Macao' },
    { code: 'MP', name: 'Northern Mariana Islands' },
    { code: 'MQ', name: 'Martinique' },
    { code: 'MR', name: 'Mauritania' },
    { code: 'MS', name: 'Montserrat' },
    { code: 'MT', name: 'Malta' },
    { code: 'MU', name: 'Mauritius' },
    { code: 'MV', name: 'Maldives' },
    { code: 'MW', name: 'Malawi' },
    { code: 'MX', name: 'Mexico' },
    { code: 'MY', name: 'Malaysia' },
    { code: 'MZ', name: 'Mozambique' },
    { code: 'NA', name: 'Namibia' },
    { code: 'NC', name: 'New Caledonia' },
    { code: 'NE', name: 'Niger' },
    { code: 'NF', name: 'Norfolk Island' },
    { code: 'NG', name: 'Nigeria' },
    { code: 'NI', name: 'Nicaragua' },
    { code: 'NL', name: 'Netherlands' },
    { code: 'NO', name: 'Norway' },
    { code: 'NP', name: 'Nepal' },
    { code: 'NR', name: 'Nauru' },
    { code: 'NU', name: 'Niue' },
    { code: 'NZ', name: 'New Zealand' },
    { code: 'OM', name: 'Oman' },
    { code: 'PA', name: 'Panama' },
    { code: 'PE', name: 'Peru' },
    { code: 'PF', name: 'French Polynesia' },
    { code: 'PG', name: 'Papua New Guinea' },
    { code: 'PH', name: 'Philippines' },
    { code: 'PK', name: 'Pakistan' },
    { code: 'PL', name: 'Poland' },
    { code: 'PM', name: 'St. Pierre & Miquelon' },
    { code: 'PN', name: 'Pitcairn Islands' },
    { code: 'PR', name: 'Puerto Rico' },
    { code: 'PS', name: 'Palestine' },
    { code: 'PT', name: 'Portugal' },
    { code: 'PW', name: 'Palau' },
    { code: 'PY', name: 'Paraguay' },
    { code: 'QA', name: 'Qatar' },
    { code: 'RE', name: 'Réunion' },
    { code: 'RO', name: 'Romania' },
    { code: 'RS', name: 'Serbia' },
    { code: 'RU', name: 'Russia' },
    { code: 'RW', name: 'Rwanda' },
    { code: 'SA', name: 'Saudi Arabia' },
    { code: 'SB', name: 'Solomon Islands' },
    { code: 'SC', name: 'Seychelles' },
    { code: 'SD', name: 'Sudan' },
    { code: 'SE', name: 'Sweden' },
    { code: 'SG', name: 'Singapore' },
    { code: 'SH', name: 'St. Helena' },
    { code: 'SI', name: 'Slovenia' },
    { code: 'SJ', name: 'Svalbard & Jan Mayen' },
    { code: 'SK', name: 'Slovakia' },
    { code: 'SL', name: 'Sierra Leone' },
    { code: 'SM', name: 'San Marino' },
    { code: 'SN', name: 'Senegal' },
    { code: 'SO', name: 'Somalia' },
    { code: 'SR', name: 'Suriname' },
    { code: 'SS', name: 'South Sudan' },
    { code: 'ST', name: 'São Tomé & Príncipe' },
    { code: 'SV', name: 'El Salvador' },
    { code: 'SX', name: 'Sint Maarten' },
    { code: 'SY', name: 'Syria' },
    { code: 'SZ', name: 'Eswatini' },
    { code: 'TA', name: 'Tristan da Cunha' },
    { code: 'TC', name: 'Turks & Caicos Islands' },
    { code: 'TD', name: 'Chad' },
    { code: 'TF', name: 'French Southern Territories' },
    { code: 'TG', name: 'Togo' },
    { code: 'TH', name: 'Thailand' },
    { code: 'TJ', name: 'Tajikistan' },
    { code: 'TK', name: 'Tokelau' },
    { code: 'TL', name: 'Timor-Leste' },
    { code: 'TM', name: 'Turkmenistan' },
    { code: 'TN', name: 'Tunisia' },
    { code: 'TO', name: 'Tonga' },
    { code: 'TR', name: 'Turkey' },
    { code: 'TT', name: 'Trinidad & Tobago' },
    { code: 'TV', name: 'Tuvalu' },
    { code: 'TW', name: 'Taiwan' },
    { code: 'TZ', name: 'Tanzania' },
    { code: 'UA', name: 'Ukraine' },
    { code: 'UG', name: 'Uganda' },
    { code: 'UM', name: 'U.S. Outlying Islands' },
    { code: 'US', name: 'United States' },
    { code: 'UY', name: 'Uruguay' },
    { code: 'UZ', name: 'Uzbekistan' },
    { code: 'VA', name: 'Vatican City' },
    { code: 'VC', name: 'St. Vincent & Grenadines' },
    { code: 'VE', name: 'Venezuela' },
    { code: 'VG', name: 'British Virgin Islands' },
    { code: 'VI', name: 'U.S. Virgin Islands' },
    { code: 'VN', name: 'Vietnam' },
    { code: 'VU', name: 'Vanuatu' },
    { code: 'WF', name: 'Wallis & Futuna' },
    { code: 'WS', name: 'Samoa' },
    { code: 'XK', name: 'Kosovo' },
    { code: 'YE', name: 'Yemen' },
    { code: 'YT', name: 'Mayotte' },
    { code: 'ZA', name: 'South Africa' },
    { code: 'ZM', name: 'Zambia' },
    { code: 'ZW', name: 'Zimbabwe' }
  ];

  let searchTerm = $state('');
  let showDropdown = $state(false);
  let isEditing = $state(false);

  // Derive the display value based on whether user is editing
  let displayValue = $derived.by(() => {
    if (isEditing) {
      return searchTerm;
    }
    if (value) {
      const region = regions.find((r) => r.code.toLowerCase() === value.toLowerCase());
      return region ? `${region.code} - ${region.name}` : '';
    }
    return '';
  });

  let filteredRegions = $derived(searchTerm ? regions.filter((r) => r.name.toLowerCase().includes(searchTerm.toLowerCase()) || r.code.toLowerCase().includes(searchTerm.toLowerCase())) : regions);

  function selectRegion(code: string) {
    value = code.toLowerCase();
    isEditing = false;
    showDropdown = false;
  }

  function onInput(e: Event) {
    searchTerm = (e.target as HTMLInputElement).value;
    isEditing = true;
    showDropdown = true;
  }

  function onFocus() {
    showDropdown = true;
  }

  function onBlur() {
    setTimeout(() => {
      showDropdown = false;
      isEditing = false;
    }, 200);
  }
</script>

<div class="relative">
  <Input type="text" value={displayValue} oninput={onInput} placeholder="Search regions..." onfocus={onFocus} onblur={onBlur} class="dark:bg-gray-600" />
  {#if showDropdown && filteredRegions.length > 0}
    <div class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-lg border border-gray-300 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-700">
      {#each filteredRegions as region (region.code)}
        <button type="button" class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-600" onclick={() => selectRegion(region.code)}>
          <span class="font-medium">{region.code}</span> - {region.name}
        </button>
      {/each}
    </div>
  {/if}
</div>
