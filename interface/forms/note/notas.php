function generate_save_field($frow, $currvalue)
{
global $rootdir, $date_init, $ISSUE_TYPES, $code_types, $membership_group_number;

$currescaped = htmlspecialchars($currvalue, ENT_QUOTES);

$data_type   = $frow['data_type'];
$field_id    = $frow['field_id'];
$list_id     = $frow['list_id'];
$backup_list = $frow['list_backup_id'];

// escaped variables to use in html
$field_id_esc= htmlspecialchars($field_id, ENT_QUOTES);
$list_id_esc = htmlspecialchars($list_id, ENT_QUOTES);

// Added 5-09 by BM - Translate description if applicable
$description = (isset($frow['description']) ? htmlspecialchars(xl_layout_label($frow['description']), ENT_QUOTES) : '');

// Support edit option T which assigns the (possibly very long) description as
// the default value.
if (isOption($frow['edit_options'], 'T') !== false) {
if (strlen($currescaped) == 0) {
$currescaped = $description;
}

// Description used in this way is not suitable as a title.
$description = '';
}

// added 5-2009 by BM to allow modification of the 'empty' text title field.
//  Can pass $frow['empty_title'] with this variable, otherwise
//  will default to 'Unassigned'.
// modified 6-2009 by BM to allow complete skipping of the 'empty' text title
//  if make $frow['empty_title'] equal to 'SKIP'
$showEmpty = true;
if (isset($frow['empty_title'])) {
if ($frow['empty_title'] == "SKIP") {
//do not display an 'empty' choice
$showEmpty = false;
$empty_title = "Unassigned";
} else {
$empty_title = $frow['empty_title'];
}
} else {
$empty_title = "Unassigned";
}

$disabled = isOption($frow['edit_options'], '0') === false ? '' : 'disabled';

$lbfchange = (
strpos($frow['form_id'], 'LBF') === 0 ||
strpos($frow['form_id'], 'LBT') === 0 ||
$frow['form_id'] == 'DEM'             ||
$frow['form_id'] == 'HIS'
) ? "checkSkipConditions();" : "";
$lbfonchange = $lbfchange ? "onchange='$lbfchange'" : "";

// generic single-selection list or Race and Ethnicity.
// These data types support backup lists.
if ($data_type == 1 || $data_type == 33) {
echo generate_select_list(
"form_$field_id",
$list_id,
$currvalue,
$description,
($showEmpty ? $empty_title : ''),
'',
$lbfchange,
'',
($disabled ? array('disabled' => 'disabled') : null),
false,
$backup_list
);
} elseif ($data_type == 2) { // simple text field
$fldlength = htmlspecialchars($frow['fld_length'], ENT_QUOTES);
$maxlength = $frow['max_length'];
$string_maxlength = "";
// if max_length is set to zero, then do not set a maxlength
if ($maxlength) {
$string_maxlength = "maxlength='".attr($maxlength)."'";
}

echo "<input type='text'" .
" class='form-control'" .
" name='$field_id_esc'" .
" id='$field_id_esc'" .
" size='$fldlength'" .
" $string_maxlength" .
" title='$description'" .
" value='$currescaped'";
$tmp = $lbfchange;
if (isOption($frow['edit_options'], 'C') !== false) {
$tmp .= "capitalizeMe(this);";
} elseif (isOption($frow['edit_options'], 'U') !== false) {
$tmp .= "this.value = this.value.toUpperCase();";
}

if ($tmp) {
echo " onchange='$tmp'";
}

$tmp = htmlspecialchars($GLOBALS['gbl_mask_patient_id'], ENT_QUOTES);
// If mask is for use at save time, treat as no mask.
if (strpos($tmp, '^') !== false) {
$tmp = '';
}
if ($field_id == 'pubpid' && strlen($tmp) > 0) {
echo " onkeyup='maskkeyup(this,\"$tmp\")'";
echo " onblur='maskblur(this,\"$tmp\")'";
}

if (isOption($frow['edit_options'], '1') !== false && strlen($currescaped) > 0) {
echo " readonly";
}

if ($disabled) {
echo ' disabled';
}

echo " />";
} elseif ($data_type == 3) { // long or multi-line text field
$textCols = htmlspecialchars($frow['fld_length'], ENT_QUOTES);
$textRows = htmlspecialchars($frow['fld_rows'], ENT_QUOTES);
echo "<textarea" .
" name='$field_id_esc'" .
" class='form-control'" .
" id='$field_id_esc'" .
" title='$description'" .
" cols='$textCols'" .
" rows='$textRows' $lbfonchange $disabled" .
">" . $currescaped . "</textarea>";
} elseif ($data_type == 4) { // date
$age_asof_date = ''; // optionalAge() sets this
$age_format = isOption($frow['edit_options'], 'A') === false ? 3 : 0;
$agestr = optionalAge($frow, $currvalue, $age_asof_date, $description);
if ($agestr) {
echo "<table cellpadding='0' cellspacing='0'><tr><td class='text'>";
            }

            $onchange_string = '';
            if (!$disabled && $agestr) {
            $onchange_string = "onchange=\"if (typeof(updateAgeString) == 'function') " .
            "updateAgeString('$field_id','$age_asof_date', $age_format, '$description')\"";
            }
            if ($data_type == 4) {
            $modtmp = isOption($frow['edit_options'], 'F') === false ? 0 : 1;
            if (!$modtmp) {
            $dateValue  = oeFormatShortDate(substr($currescaped, 0, 10));
            echo "<input type='text' size='10' class='datepicker form-control' name='$field_id_esc' id='$field_id_esc'" .
            " value='" .  attr($dateValue)  ."'";
            } else {
            $dateValue  = oeFormatDateTime(substr($currescaped, 0, 20), 0);
            echo "<input type='text' size='20' class='datetimepicker form-control' name='$field_id_esc' id='$field_id_esc'" .
            " value='" . attr($dateValue) . "'";
            }
            }
            if (!$agestr) {
            echo " title='$description'";
            }

            echo " $onchange_string $lbfonchange $disabled />";

            // Optional display of age or gestational age.
            if ($agestr) {
            echo "</td></tr><tr><td id='span_$field_id' class='text'>" . text($agestr) . "</td></tr></table>";
}
} elseif ($data_type == 10) { // provider list, local providers only
$ures = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
"WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
"AND authorized = 1 " .
"ORDER BY lname, fname");
echo "<select name='$field_id_esc' id='$field_id_esc' title='$description' $lbfonchange $disabled class='form-control'>";
    echo "<option value=''>" . xlt($empty_title) . "</option>";
    $got_selected = false;
    while ($urow = sqlFetchArray($ures)) {
    $uname = text($urow['fname'] . ' ' . $urow['lname']);
    $optionId = attr($urow['id']);
    echo "<option value='$optionId'";
    if ($urow['id'] == $currvalue) {
    echo " selected";
    $got_selected = true;
    }

    echo ">$uname</option>";
    }

    if (!$got_selected && $currvalue) {
    echo "<option value='" . attr($currvalue) . "' selected>* " . text($currvalue) . " *</option>";
    echo "</select>";
echo " <font color='red' title='" . xla('Please choose a valid selection from the list.') . "'>" . xlt('Fix this') . "!</font>";
} else {
echo "</select>";
}
} elseif ($data_type == 11) { // provider list, including address book entries with an NPI number
$ures = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
"WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
"AND ( authorized = 1 OR ( username = '' AND npi != '' ) ) " .
"ORDER BY lname, fname");
echo "<select name='$field_id_esc' id='$field_id_esc' title='$description' class='form-control'";
echo " $lbfonchange $disabled>";
echo "<option value=''>" . xlt('Unassigned') . "</option>";
$got_selected = false;
while ($urow = sqlFetchArray($ures)) {
$uname = text($urow['fname'] . ' ' . $urow['lname']);
$optionId = attr($urow['id']);
echo "<option value='$optionId'";
if ($urow['id'] == $currvalue) {
echo " selected";
$got_selected = true;
}

echo ">$uname</option>";
}

if (!$got_selected && $currvalue) {
echo "<option value='" . attr($currvalue) . "' selected>* " . text($currvalue) . " *</option>";
echo "</select>";
echo " <font color='red' title='" . xla('Please choose a valid selection from the list.') . "'>" . xlt('Fix this') . "!</font>";
} else {
echo "</select>";
}
} elseif ($data_type == 12) { // pharmacy list
echo "<select name='$field_id_esc' id='$field_id_esc' title='$description' class='form-control'";
echo " $lbfonchange $disabled>";
echo "<option value='0'></option>";
$pres = get_pharmacies();
$got_selected = false;
$zone ='';
while ($prow = sqlFetchArray($pres)) {
if ($zone != strtolower(trim($prow['city']))) {
if ($zone !='') {
echo "</optgroup>";
}
$zone = strtolower(trim($prow['city']));
echo "<optgroup label='".attr($prow['city'])."'>";
}
$key = $prow['id'];
$optionValue = htmlspecialchars($key, ENT_QUOTES);
$optionLabel = htmlspecialchars($prow['name'] . ' ' . $prow['area_code'] . '-' .
$prow['prefix'] . '-' . $prow['number'] . ' / ' .
$prow['line1'] . ' / ' . $prow['city'], ENT_NOQUOTES);
echo "<option value='$optionValue'";
if ($currvalue == $key) {
echo " selected";
$got_selected = true;
}

echo ">$optionLabel</option>";
}

if (!$got_selected && $currvalue) {
echo "<option value='" . attr($currvalue) . "' selected>* " . text($currvalue) . " *</option>";
echo "</select>";
echo " <font color='red' title='" . xla('Please choose a valid selection from the list.') . "'>" . xlt('Fix this') . "!</font>";
} else {
echo "</select>";
}
} elseif ($data_type == 13) { // squads
echo "<select name='$field_id_esc' id='$field_id_esc' title='$description' class='form-control'";
echo " $lbfonchange $disabled>";
echo "<option value=''>&nbsp;</option>";
$squads = acl_get_squads();
if ($squads) {
foreach ($squads as $key => $value) {
$optionValue = htmlspecialchars($key, ENT_QUOTES);
$optionLabel = htmlspecialchars($value[3], ENT_NOQUOTES);
echo "<option value='$optionValue'";
if ($currvalue == $key) {
echo " selected";
}

echo ">$optionLabel</option>\n";
}
}

echo "</select>";
} elseif ($data_type == 14) {
// Address book, preferring organization name if it exists and is not in
// parentheses, and excluding local users who are not providers.
// Supports "referred to" practitioners and facilities.
// Alternatively the letter L in edit_options means that abook_type
// must be "ord_lab", indicating types used with the procedure
// lab ordering system.
// Alternatively the letter O in edit_options means that abook_type
// must begin with "ord_", indicating types used with the procedure
// ordering system.
// Alternatively the letter V in edit_options means that abook_type
// must be "vendor", indicating the Vendor type.
// Alternatively the letter R in edit_options means that abook_type
// must be "dist", indicating the Distributor type.

if (isOption($frow['edit_options'], 'L') !== false) {
$tmp = "abook_type = 'ord_lab'";
} elseif (isOption($frow['edit_options'], 'O') !== false) {
$tmp = "abook_type LIKE 'ord\\_%'";
} elseif (isOption($frow['edit_options'], 'V') !== false) {
$tmp = "abook_type LIKE 'vendor%'";
} elseif (isOption($frow['edit_options'], 'R') !== false) {
$tmp = "abook_type LIKE 'dist'";
} else {
$tmp = "( username = '' OR authorized = 1 )";
}

$ures = sqlStatement("SELECT id, fname, lname, organization, username FROM users " .
"WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
"AND $tmp " .
"ORDER BY organization, lname, fname");
echo "<select name='$field_id_esc' id='$field_id_esc' title='$description' class='form-control'";
echo " $lbfonchange $disabled>";
echo "<option value=''>" . htmlspecialchars(xl('Unassigned'), ENT_NOQUOTES) . "</option>";
while ($urow = sqlFetchArray($ures)) {
$uname = $urow['organization'];
if (empty($uname) || substr($uname, 0, 1) == '(') {
$uname = $urow['lname'];
if ($urow['fname']) {
$uname .= ", " . $urow['fname'];
}
}

$optionValue = htmlspecialchars($urow['id'], ENT_QUOTES);
$optionLabel = htmlspecialchars($uname, ENT_NOQUOTES);
echo "<option value='$optionValue'";
// Failure to translate Local and External is not an error here;
// they are only used as internal flags and must not be translated!
$title = $urow['username'] ? 'Local' : 'External';
$optionTitle = htmlspecialchars($title, ENT_QUOTES);
echo " title='$optionTitle'";
if ($urow['id'] == $currvalue) {
echo " selected";
}

echo ">$optionLabel</option>";
}

echo "</select>";
} elseif ($data_type == 15) { // A billing code. If description matches an existing code type then that type is used.
$codetype = '';
if (!empty($frow['description']) && isset($code_types[$frow['description']])) {
$codetype = $frow['description'];
}
$fldlength = htmlspecialchars($frow['fld_length'], ENT_QUOTES);
$maxlength = $frow['max_length'];
$string_maxlength = "";
// if max_length is set to zero, then do not set a maxlength
if ($maxlength) {
$string_maxlength = "maxlength='".attr($maxlength)."'";
}

//
if (isOption($frow['edit_options'], '2') !== false && substr($frow['form_id'], 0, 3) == 'LBF') {
// Option "2" generates a hidden input for the codes, and a matching visible field
// displaying their descriptions. First step is computing the description string.
$currdescstring = '';
if (!empty($currvalue)) {
$relcodes = explode(';', $currvalue);
foreach ($relcodes as $codestring) {
if ($codestring === '') {
continue;
}

$code_text = lookup_code_descriptions($codestring);
if ($currdescstring !== '') {
$currdescstring .= '; ';
}

if (!empty($code_text)) {
$currdescstring .= $code_text;
} else {
$currdescstring .= $codestring;
}
}
}

$currdescstring = attr($currdescstring);
//
echo "<input type='text'" .
" name='$field_id_esc'" .
" id='form_related_code'" .
" size='$fldlength'" .
" value='$currescaped'" .
" style='display:none'" .
" $lbfonchange readonly $disabled />";
// Extra readonly input field for optional display of code description(s).
echo "<input type='text'" .
" name='$field_id_esc" . "__desc'" .
" size='$fldlength'" .
" title='$description'" .
" value='$currdescstring'";
if (!$disabled) {
echo " onclick='sel_related(this,\"$codetype\")'";
}

echo "class='form-control'";
echo " readonly $disabled />";
} else {
echo "<input type='text'" .
" name='$field_id_esc'" .
" id='form_related_code'" .
" size='$fldlength'" .
" $string_maxlength" .
" title='$description'" .
" value='$currescaped'";
if (!$disabled) {
echo " onclick='sel_related(this,\"$codetype\")'";
}

echo "class='form-control'";
echo " $lbfonchange readonly $disabled />";
}
} elseif ($data_type == 16) { // insurance company list
echo "<select name='$field_id_esc' id='$field_id_esc' class='form-control' title='$description'>";
    echo "<option value='0'></option>";
    $insprovs = getInsuranceProviders();
    $got_selected = false;
    foreach ($insprovs as $key => $ipname) {
    $optionValue = htmlspecialchars($key, ENT_QUOTES);
    $optionLabel = htmlspecialchars($ipname, ENT_NOQUOTES);
    echo "<option value='$optionValue'";
    if ($currvalue == $key) {
    echo " selected";
    $got_selected = true;
    }

    echo ">$optionLabel</option>";
    }

    if (!$got_selected && $currvalue) {
    echo "<option value='" . attr($currvalue) . "' selected>* " . text($currvalue) . " *</option>";
    echo "</select>";
echo " <font color='red' title='" . xla('Please choose a valid selection from the list.') . "'>" . xlt('Fix this') . "!</font>";
} else {
echo "</select>";
}
} elseif ($data_type == 17) { // issue types
echo "<select name='$field_id_esc' id='$field_id_esc' class='form-control' title='$description'>";
    echo "<option value='0'></option>";
    $got_selected = false;
    foreach ($ISSUE_TYPES as $key => $value) {
    $optionValue = htmlspecialchars($key, ENT_QUOTES);
    $optionLabel = htmlspecialchars($value[1], ENT_NOQUOTES);
    echo "<option value='$optionValue'";
    if ($currvalue == $key) {
    echo " selected";
    $got_selected = true;
    }

    echo ">$optionLabel</option>";
    }

    if (!$got_selected && strlen($currvalue) > 0) {
    echo "<option value='" . attr($currvalue) . "' selected>* " . text($currvalue) . " *</option>";
    echo "</select>";
echo " <font color='red' title='" . xla('Please choose a valid selection from the list.') . "'>" . xlt('Fix this') . "!</font>";
} else {
echo "</select>";
}
} elseif ($data_type == 18) { // Visit categories.
$cres = sqlStatement("SELECT pc_catid, pc_catname " .
"FROM openemr_postcalendar_categories ORDER BY pc_catname");
echo "<select name='$field_id_esc' id='$field_id_esc' class='form-control' title='$description'" .
" $lbfonchange $disabled>";
echo "<option value=''>" . xlt($empty_title) . "</option>";
$got_selected = false;
while ($crow = sqlFetchArray($cres)) {
$catid = $crow['pc_catid'];
if (($catid < 9 && $catid != 5) || $catid == 11) {
continue;
}

echo "<option value='" . attr($catid) . "'";
if ($catid == $currvalue) {
echo " selected";
$got_selected = true;
}

echo ">" . text(xl_appt_category($crow['pc_catname'])) . "</option>";
}

if (!$got_selected && $currvalue) {
echo "<option value='" . attr($currvalue) . "' selected>* " . text($currvalue) . " *</option>";
echo "</select>";
echo " <font color='red' title='" . xla('Please choose a valid selection from the list.') . "'>" . xlt('Fix this') . "!</font>";
} else {
echo "</select>";
}
} elseif ($data_type == 21) { // a set of labeled checkboxes
// If no list then it's a single checkbox and its value is "Yes" or empty.
if (!$list_id) {
echo "<input type='checkbox' name='form_{$field_id_esc}' " .
"id='form_{$field_id_esc}' value='Yes' $lbfonchange";
if ($currvalue) {
echo " checked";
}
echo " $disabled />";
} else {
// In this special case, fld_length is the number of columns generated.
$cols = max(1, $frow['fld_length']);
$avalue = explode('|', $currvalue);
$lres = sqlStatement("SELECT * FROM list_options " .
"WHERE list_id = ? AND activity = 1 ORDER BY seq, title", array($list_id));
echo "<table cellpadding='0' cellspacing='0' width='100%' title='".attr($description)."'>";
    $tdpct = (int) (100 / $cols);
    for ($count = 0; $lrow = sqlFetchArray($lres); ++$count) {
    $option_id = $lrow['option_id'];
    $option_id_esc = htmlspecialchars($option_id, ENT_QUOTES);
    // if ($count) echo "<br />";
    if ($count % $cols == 0) {
    if ($count) {
    echo "</tr>";
    }
    echo "<tr>";
        }
        echo "<td width='" . attr($tdpct) . "%' nowrap>";
            echo "<input type='checkbox' name='form_{$field_id_esc}[$option_id_esc]'" .
            "id='form_{$field_id_esc}[$option_id_esc]' class='form-control' value='1' $lbfonchange";
            if (in_array($option_id, $avalue)) {
            echo " checked";
            }
            // Added 5-09 by BM - Translate label if applicable
            echo " $disabled />" . htmlspecialchars(xl_list_label($lrow['title']), ENT_NOQUOTES);
            echo "</td>";
        }
        if ($count) {
        echo "</tr>";
    if ($count > $cols) {
    // Add some space after multiple rows of checkboxes.
    $cols = htmlspecialchars($cols, ENT_QUOTES);
    echo "<tr><td colspan='$cols' style='height:0.7em'></td></tr>";
    }
    }
    echo "</table>";
}
} elseif ($data_type == 22) { // a set of labeled text input fields
$tmp = explode('|', $currvalue);
$avalue = array();
foreach ($tmp as $value) {
if (preg_match('/^([^:]+):(.*)$/', $value, $matches)) {
$avalue[$matches[1]] = $matches[2];
}
}

$lres = sqlStatement("SELECT * FROM list_options " .
"WHERE list_id = ? AND activity = 1 ORDER BY seq, title", array($list_id));
echo "<table cellpadding='0' cellspacing='0'>";
    while ($lrow = sqlFetchArray($lres)) {
    $option_id = $lrow['option_id'];
    $option_id_esc = htmlspecialchars($option_id, ENT_QUOTES);
    $maxlength = $frow['max_length'];
    $string_maxlength = "";
    // if max_length is set to zero, then do not set a maxlength
    if ($maxlength) {
    $string_maxlength = "maxlength='".attr($maxlength)."'";
    }

    $fldlength = empty($frow['fld_length']) ?  20 : $frow['fld_length'];

    // Added 5-09 by BM - Translate label if applicable
    echo "<tr><td>" . htmlspecialchars(xl_list_label($lrow['title']), ENT_NOQUOTES) . "&nbsp;</td>";
        $fldlength = htmlspecialchars($fldlength, ENT_QUOTES);
        $optionValue = htmlspecialchars($avalue[$option_id], ENT_QUOTES);
        echo "<td><input type='text'" .
            " name='form_{$field_id_esc}[$option_id_esc]'" .
            " id='form_{$field_id_esc}[$option_id_esc]'" .
            " size='$fldlength'" .
            " class='form-control'" .
            " $string_maxlength" .
            " value='$optionValue'";
            echo " $lbfonchange $disabled /></td></tr>";
    }

    echo "</table>";
} elseif ($data_type == 23) { // a set of exam results; 3 radio buttons and a text field:
$tmp = explode('|', $currvalue);
$avalue = array();
foreach ($tmp as $value) {
if (preg_match('/^([^:]+):(.*)$/', $value, $matches)) {
$avalue[$matches[1]] = $matches[2];
}
}

$maxlength = $frow['max_length'];
$string_maxlength = "";
// if max_length is set to zero, then do not set a maxlength
if ($maxlength) {
$string_maxlength = "maxlength='".attr($maxlength)."'";
}

$fldlength = empty($frow['fld_length']) ?  20 : $frow['fld_length'];
$lres = sqlStatement("SELECT * FROM list_options " .
"WHERE list_id = ? AND activity = 1 ORDER BY seq, title", array($list_id));
echo "<table cellpadding='0' cellspacing='0'>";
    echo "<tr><td>&nbsp;</td><td class='bold'>" .
            htmlspecialchars(xl('N/A'), ENT_NOQUOTES) .
            "&nbsp;</td><td class='bold'>" .
            htmlspecialchars(xl('Nor'), ENT_NOQUOTES) . "&nbsp;</td>" .
        "<td class='bold'>" .
            htmlspecialchars(xl('Abn'), ENT_NOQUOTES) . "&nbsp;</td><td class='bold'>" .
            htmlspecialchars(xl('Date/Notes'), ENT_NOQUOTES) . "</td></tr>";
    while ($lrow = sqlFetchArray($lres)) {
    $option_id = $lrow['option_id'];
    $option_id_esc = htmlspecialchars($option_id, ENT_QUOTES);
    $restype = substr($avalue[$option_id], 0, 1);
    $resnote = substr($avalue[$option_id], 2);

    // Added 5-09 by BM - Translate label if applicable
    echo "<tr><td>" . htmlspecialchars(xl_list_label($lrow['title']), ENT_NOQUOTES) . "&nbsp;</td>";

        for ($i = 0; $i < 3; ++$i) {
        $inputValue = htmlspecialchars($i, ENT_QUOTES);
        echo "<td><input type='radio'" .
            " name='radio_{$field_id_esc}[$option_id_esc]'" .
            " id='radio_{$field_id_esc}[$option_id_esc]'" .
            " value='$inputValue' $lbfonchange";
            if ($restype === "$i") {
            echo " checked";
            }

            echo " $disabled /></td>";
        }

        $fldlength = htmlspecialchars($fldlength, ENT_QUOTES);
        $resnote = htmlspecialchars($resnote, ENT_QUOTES);
        echo "<td><input type='text'" .
            " name='form_{$field_id_esc}[$option_id_esc]'" .
            " id='form_{$field_id_esc}[$option_id_esc]'" .
            " size='$fldlength'" .
            " $string_maxlength" .
            " value='$resnote' $disabled /></td>";
        echo "</tr>";
    }

    echo "</table>";
} elseif ($data_type == 24) { // the list of active allergies for the current patient
// this is read-only!
$query = "SELECT title, comments FROM lists WHERE " .
"pid = ? AND type = 'allergy' AND enddate IS NULL " .
"ORDER BY begdate";
// echo "<!-- $query -->\n"; // debugging
$lres = sqlStatement($query, array($GLOBALS['pid']));
$count = 0;
while ($lrow = sqlFetchArray($lres)) {
if ($count++) {
echo "<br />";
}

echo htmlspecialchars($lrow['title'], ENT_NOQUOTES);
if ($lrow['comments']) {
echo ' (' . htmlspecialchars($lrow['comments'], ENT_NOQUOTES) . ')';
}
}
} elseif ($data_type == 25) { // a set of labeled checkboxes, each with a text field:
$tmp = explode('|', $currvalue);
$avalue = array();
foreach ($tmp as $value) {
if (preg_match('/^([^:]+):(.*)$/', $value, $matches)) {
$avalue[$matches[1]] = $matches[2];
}
}

$maxlength = $frow['max_length'];
$string_maxlength = "";
// if max_length is set to zero, then do not set a maxlength
if ($maxlength) {
$string_maxlength = "maxlength='".attr($maxlength)."'";
}

$fldlength = empty($frow['fld_length']) ?  20 : $frow['fld_length'];
$lres = sqlStatement("SELECT * FROM list_options " .
"WHERE list_id = ? AND activity = 1 ORDER BY seq, title", array($list_id));
echo "<table cellpadding='0' cellspacing='0'>";
    while ($lrow = sqlFetchArray($lres)) {
    $option_id = $lrow['option_id'];
    $option_id_esc = htmlspecialchars($option_id, ENT_QUOTES);
    $restype = substr($avalue[$option_id], 0, 1);
    $resnote = substr($avalue[$option_id], 2);

    // Added 5-09 by BM - Translate label if applicable
    echo "<tr><td>" . htmlspecialchars(xl_list_label($lrow['title']), ENT_NOQUOTES) . "&nbsp;</td>";

        $option_id = htmlspecialchars($option_id, ENT_QUOTES);
        echo "<td><input type='checkbox' name='check_{$field_id_esc}[$option_id_esc]'" .
            " id='check_{$field_id_esc}[$option_id_esc]' class='form-control' value='1' $lbfonchange";
            if ($restype) {
            echo " checked";
            }

            echo " $disabled />&nbsp;</td>";
        $fldlength = htmlspecialchars($fldlength, ENT_QUOTES);
        $resnote = htmlspecialchars($resnote, ENT_QUOTES);
        echo "<td><input type='text'" .
            " name='form_{$field_id_esc}[$option_id_esc]'" .
            " id='form_{$field_id_esc}[$option_id_esc]'" .
            " size='$fldlength'" .
            " class='form-control' " .
            " $string_maxlength" .
            " value='$resnote' $disabled /></td>";
        echo "</tr>";
    }

    echo "</table>";
} elseif ($data_type == 26) { // single-selection list with ability to add to it
echo generate_select_list(
"form_$field_id",
$list_id,
$currvalue,
$description,
($showEmpty ? $empty_title : ''),
'addtolistclass_'.$list_id,
$lbfchange,
'',
($disabled ? array('disabled' => 'disabled') : null),
false,
$backup_list
);
// show the add button if user has access to correct list
$inputValue = htmlspecialchars(xl('Add'), ENT_QUOTES);
$outputAddButton = "<input type='button' id='addtolistid_" . $list_id_esc . "' fieldid='form_" .
        $field_id_esc . "' class='addtolist' value='$inputValue' $disabled />";
if (aco_exist('lists', $list_id)) {
// a specific aco exist for this list, so ensure access
if (acl_check('lists', $list_id)) {
echo $outputAddButton;
}
} else {
// no specific aco exist for this list, so check for access to 'default' list
if (acl_check('lists', 'default')) {
echo $outputAddButton;
}
}
} elseif ($data_type == 27) { // a set of labeled radio buttons
// In this special case, fld_length is the number of columns generated.
$cols = max(1, $frow['fld_length']);
// Support for edit option M.
if (isOption($frow['edit_options'], 'M')) {
++$membership_group_number;
}
//
$lres = sqlStatement("SELECT * FROM list_options " .
"WHERE list_id = ? AND activity = 1 ORDER BY seq, title", array($list_id));
echo "<table cellpadding='0' cellspacing='0' width='100%'>";
    $tdpct = (int) (100 / $cols);
    $got_selected = false;
    for ($count = 0; $lrow = sqlFetchArray($lres); ++$count) {
    $option_id = $lrow['option_id'];
    $option_id_esc = htmlspecialchars($option_id, ENT_QUOTES);
    if ($count % $cols == 0) {
    if ($count) {
    echo "</tr>";
    }
    echo "<tr>";
        }
        echo "<td width='" . attr($tdpct) . "%'>";
            echo "<input type='radio' name='form_{$field_id_esc}' id='form_{$field_id_esc}[$option_id_esc]'" .
            " value='$option_id_esc' $lbfonchange";
            // Support for edit options M and m.
            if (isOption($frow['edit_options'], 'M')) {
            echo " class='form-control'";
            echo " onclick='checkGroupMembers(this, $membership_group_number);'";
            } else if (isOption($frow['edit_options'], 'm')) {
            echo " class='form-control lbf_memgroup_$membership_group_number'";
            } else {
            echo " class='form-control'";
            }
            //
            if ((strlen($currvalue) == 0 && $lrow['is_default']) ||
            (strlen($currvalue)  > 0 && $option_id == $currvalue)) {
            echo " checked";
            $got_selected = true;
            }
            echo " $disabled />" . htmlspecialchars(xl_list_label($lrow['title']), ENT_NOQUOTES);
            echo "</td>";
        }

        if ($count) {
        echo "</tr>";
    if ($count > $cols) {
    // Add some space after multiple rows of radio buttons.
    $cols = htmlspecialchars($cols, ENT_QUOTES);
    echo "<tr><td colspan='$cols' style='height:0.7em'></td></tr>";
    }
    }

    echo "</table>";
if (!$got_selected && strlen($currvalue) > 0) {
$fontTitle = htmlspecialchars(xl('Please choose a valid selection.'), ENT_QUOTES);
$fontText = htmlspecialchars(xl('Fix this'), ENT_NOQUOTES);
echo "$currescaped <font color='red' title='$fontTitle'>$fontText!</font>";
}
} elseif ($data_type == 28 || $data_type == 32) { // special case for history of lifestyle status; 3 radio buttons
// and a date text field:
// VicarePlus :: A selection list box for smoking status:
$tmp = explode('|', $currvalue);
switch (count($tmp)) {
case "4":
$resnote = $tmp[0];
$restype = $tmp[1];
$resdate = oeFormatShortDate($tmp[2]);
$reslist = $tmp[3];
break;
case "3":
$resnote = $tmp[0];
$restype = $tmp[1];
$resdate = oeFormatShortDate($tmp[2]);
break;
case "2":
$resnote = $tmp[0];
$restype = $tmp[1];
$resdate = "";
break;
case "1":
$resnote = $tmp[0];
$resdate = $restype = "";
break;
default:
$restype = $resdate = $resnote = "";
break;
}

$maxlength = $frow['max_length'];
$string_maxlength = "";
// if max_length is set to zero, then do not set a maxlength
if ($maxlength) {
$string_maxlength = "maxlength='".attr($maxlength)."'";
}

$fldlength = empty($frow['fld_length']) ?  20 : $frow['fld_length'];

$fldlength = htmlspecialchars($fldlength, ENT_QUOTES);
$resnote = htmlspecialchars($resnote, ENT_QUOTES);
$resdate = htmlspecialchars($resdate, ENT_QUOTES);
echo "<table cellpadding='0' cellspacing='0'>";
    echo "<tr>";
        if ($data_type == 28) {
        // input text
        echo "<td><input type='text'" .
            " name='$field_id_esc'" .
            " id='$field_id_esc'" .
            " size='$fldlength'" .
            " $string_maxlength" .
            " value='$resnote' $disabled />&nbsp;</td>";
        echo "<td class='bold'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".
            "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".
            htmlspecialchars(xl('Status'), ENT_NOQUOTES).":&nbsp;&nbsp;</td>";
        } elseif ($data_type == 32) {
        // input text
        echo "<tr><td><input type='text'" .
            " name='form_text_$field_id_esc'" .
            " id='form_text_$field_id_esc'" .
            " size='$fldlength'" .
            " class='form-control'" .
            " $string_maxlength" .
            " value='$resnote' $disabled />&nbsp;</td></tr>";
    echo "<td>";
        //Selection list for smoking status
        $onchange = 'radioChange(this.options[this.selectedIndex].value)';//VicarePlus :: The javascript function for selection list.
        echo generate_select_list(
        "form_$field_id",
        $list_id,
        $reslist,
        $description,
        ($showEmpty ? $empty_title : ''),
        '',
        $onchange,
        '',
        ($disabled ? array('disabled' => 'disabled') : null)
        );
        echo "</td>";
    echo "<td class='bold'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . xlt('Status') . ":&nbsp;&nbsp;</td>";
    }

    // current
    echo "<td class='text' ><input type='radio'" .
        " name='radio_{$field_id_esc}'" .
        " id='radio_{$field_id_esc}[current]'" .
        " class='form-control'" .
        " value='current" . $field_id_esc . "' $lbfonchange";
        if ($restype == "current" . $field_id) {
        echo " checked";
        }

        if ($data_type == 32) {
        echo " onClick='smoking_statusClicked(this)'";
        }

        echo " />" . xlt('Current') . "&nbsp;</td>";
    // quit
    echo "<td class='text'><input type='radio'" .
        " name='radio_{$field_id_esc}'" .
        " id='radio_{$field_id_esc}[quit]'" .
        " class='form-control'" .
        " value='quit".$field_id_esc."' $lbfonchange";
        if ($restype == "quit" . $field_id) {
        echo " checked";
        }

        if ($data_type == 32) {
        echo " onClick='smoking_statusClicked(this)'";
        }

        echo " $disabled />" . xlt('Quit') . "&nbsp;</td>";
    // quit date
    echo "<td class='text'><input type='text' size='6' class='datepicker' name='date_$field_id_esc' id='date_$field_id_esc'" .
        " value='$resdate'" .
        " title='$description'" .
        " $disabled />";
        echo "&nbsp;</td>";
    // never
    echo "<td class='text'><input type='radio'" .
        " name='radio_{$field_id_esc}'" .
        " class='form-control'" .
        " id='radio_{$field_id_esc}[never]'" .
        " value='never" . $field_id_esc . "' $lbfonchange";
        if ($restype == "never" . $field_id) {
        echo " checked";
        }

        if ($data_type == 32) {
        echo " onClick='smoking_statusClicked(this)'";
        }

        echo " />" . xlt('Never') . "&nbsp;</td>";
    // Not Applicable
    echo "<td class='text'><input type='radio'" .
        " class='form-control' " .
        " name='radio_{$field_id}'" .
        " id='radio_{$field_id}[not_applicable]'" .
        " value='not_applicable" . $field_id . "' $lbfonchange";
        if ($restype == "not_applicable" . $field_id) {
        echo " checked";
        }

        if ($data_type == 32) {
        echo " onClick='smoking_statusClicked(this)'";
        }

        echo " $disabled />" . xlt('N/A') . "&nbsp;</td>";
    //
    //Added on 5-jun-2k14 (regarding 'Smoking Status - display SNOMED code description')
    echo "<td class='text' ><div id='smoke_code'></div></td>";
    echo "</tr>";
    echo "</table>";
} elseif ($data_type == 31) { // static text.  read-only, of course.
echo parse_static_text($frow);
} elseif ($data_type == 34) {
// $data_type == 33
// Race and Ethnicity. After added support for backup lists, this is now the same as datatype 1; so have migrated it there.
// $data_type == 33

$arr = explode("|*|*|*|", $currvalue);
echo "<a href='../../../library/custom_template/custom_template.php?type=form_{$field_id}&contextName=".htmlspecialchars($list_id_esc, ENT_QUOTES)."' class='iframe_medium' style='text-decoration:none;color:black;'>";
    echo "<div id='form_{$field_id}_div' class='text-area' style='min-width:100pt'>" . $arr[0] . "</div>";
    echo "<div style='display:none'><textarea name='form_{$field_id}' id='form_{$field_id}' class='form-control' style='display:none' $lbfonchange $disabled>" . $currvalue . "</textarea></div>";
    echo "</a>";
} elseif ($data_type == 35) { //facilities drop-down list
if (empty($currvalue)) {
$currvalue = 0;
}

dropdown_facility(
$selected = $currvalue,
$name = "form_$field_id_esc",
$allow_unspecified = true,
$allow_allfacilities = false,
$disabled,
$lbfchange
);
} elseif ($data_type == 36) { //multiple select, supports backup list
echo generate_select_list(
"form_$field_id",
$list_id,
$currvalue,
$description,
$showEmpty ? $empty_title : '',
'',
$lbfchange,
'',
null,
true,
$backup_list
);
} elseif ($data_type == 40) { // Canvas and related elements for browser-side image drawing.
// Note you must invoke lbf_canvas_head() (below) to use this field type in a form.
// Unlike other field types, width and height are in pixels.
$canWidth  = intval($frow['fld_length']);
$canHeight = intval($frow['fld_rows']);
if (empty($currvalue)) {
if (preg_match('/\\bimage=([a-zA-Z0-9._-]*)/', $frow['description'], $matches)) {
// If defined this is the filename of the default starting image.
$currvalue = $GLOBALS['web_root'] . '/sites/' . $_SESSION['site_id'] . '/images/' . $matches[1];
}
}

$mywidth  = 50 + ($canWidth  > 250 ? $canWidth  : 250);
$myheight = 31 + ($canHeight > 261 ? $canHeight : 261);
echo "<div id='$field_id_esc' style='width:$mywidth; height:$myheight;'></div>";
// Hidden form field exists to send updated data to the server at submit time.
echo "<input type='hidden' name='$field_id_esc' value='' />";
// Hidden image exists to support initialization of the canvas.
echo "<img src='" . attr($currvalue) . "' id='form_{$field_id_esc}_img' style='display:none'>";
// $date_init is a misnomer but it's the place for browser-side setup logic.
$date_init .= " lbfCanvasSetup('$field_id_esc', $canWidth, $canHeight);\n";
}
}
