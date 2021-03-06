<?php
/**
 * Template form for giving consent.
 *
 * Parameters:
 * - 'srcMetadata': Metadata/configuration for the source.
 * - 'dstMetadata': Metadata/configuration for the destination.
 * - 'yesTarget': Target URL for the yes-button. This URL will receive a POST request.
 * - 'yesData': Parameters which should be included in the yes-request.
 * - 'noTarget': Target URL for the no-button. This URL will receive a GET request.
 * - 'noData': Parameters which should be included in the no-request.
 * - 'attributes': The attributes which are about to be released.
 * - 'sppp': URL to the privacy policy of the destination, or FALSE.
 *
 * @package SimpleSAMLphp
 */
assert('is_array($this->data["srcMetadata"])');
assert('is_array($this->data["dstMetadata"])');
assert('is_string($this->data["yesTarget"])');
assert('is_array($this->data["yesData"])');
assert('is_string($this->data["noTarget"])');
assert('is_array($this->data["noData"])');
assert('is_array($this->data["attributes"])');
assert('is_array($this->data["hiddenAttributes"])');
assert('$this->data["sppp"] === false || is_string($this->data["sppp"])');

// Parse parameters
if (array_key_exists('name', $this->data['srcMetadata'])) {
    $srcName = $this->data['srcMetadata']['name'];
} elseif (array_key_exists('OrganizationDisplayName', $this->data['srcMetadata'])) {
    $srcName = $this->data['srcMetadata']['OrganizationDisplayName'];
} else {
    $srcName = $this->data['srcMetadata']['entityid'];
}

if (is_array($srcName)) {
    $srcName = $this->t($srcName);
}

if (array_key_exists('name', $this->data['dstMetadata'])) {
    $dstName = $this->data['dstMetadata']['name'];
} elseif (array_key_exists('OrganizationDisplayName', $this->data['dstMetadata'])) {
    $dstName = $this->data['dstMetadata']['OrganizationDisplayName'];
} else {
    $dstName = $this->data['dstMetadata']['entityid'];
}

if (is_array($dstName)) {
    $dstName = $this->t($dstName);
}

$srcName = htmlspecialchars($srcName);
$dstName = htmlspecialchars($dstName);

$attributes = $this->data['attributes'];

$this->data['header'] = $this->t('{consent:consent:consent_header}');

$this->data['head'] = '<link rel="stylesheet" media="screen" type="text/css" href="' . SimpleSAML\Module::getModuleUrl('consent/style.css')  . '" />';
$this->data['head'] .= '<link rel="stylesheet" media="screen" type="text/css" href="' . SimpleSAML\Module::getModuleUrl('cesnet/res/css/consent.css')  . '" />';

$this->includeAtTemplateBase('includes/header.php');
?>


<?php

if (array_key_exists('descr_purpose', $this->data['dstMetadata'])) {
    echo '</p><p>' . $this->t(
        '{consent:consent:consent_purpose}',
        array(
            'SPNAME' => $dstName,
            'SPDESC' => $this->getTranslation(
                SimpleSAML\Utils\Arrays::arrayize(
                    $this->data['dstMetadata']['descr_purpose'],
                    'en'
                )
            ),
        )
    );
}
?>

<?php
if ($this->data['sppp'] !== false) {
    echo "<p>" . htmlspecialchars($this->t('{consent:consent:consent_privacypolicy}')) . " ";
    echo "<a target='_blank' href='" . htmlspecialchars($this->data['sppp']) . "'>" . $dstName . "</a>";
    echo "</p>";
}

echo '<h3 id="attributeheader">' .
    $this->t(
        '{perun:consent:consent_attributes_header}',
        array( 'SPNAME' => $dstName, 'IDPNAME' => $srcName)
    ) .
    '</h3>';

echo present_attributes($this, $attributes, '');

?>

<div class="row">
<div class="col-xs-6">


    <form action="<?php echo htmlspecialchars($this->data['yesTarget']); ?>">
        <?php
        if ($this->data['usestorage']) {
            $checked = ($this->data['checked'] ? 'checked="checked"' : '');
            echo '<div class="checkbox">
    	        <label>
      		    <input type="checkbox" name="saveconsent" value="1" /> ' . $this->t('{perun:consent:remember}') . '
	            </label>    
                </div>';
        }
        ?>

<?php
// Embed hidden fields...
foreach ($this->data['yesData'] as $name => $value) {
    echo '<input type="hidden" name="' . htmlspecialchars($name) .
        '" value="' . htmlspecialchars($value) . '" />';
}
?>

        <button type="submit" name="yes" class="btn btn-lg btn-success btn-block" id="yesbutton">
        <?php echo htmlspecialchars($this->t('{consent:consent:yes}')) ?>
        </button>


    </form>


</div>
<div class="col-xs-6">


    <form action="<?php echo htmlspecialchars($this->data['noTarget']); ?>">

<?php
foreach ($this->data['noData'] as $name => $value) {
    echo('<input type="hidden" name="' . htmlspecialchars($name) .
        '" value="' . htmlspecialchars($value) . '" />');
}
?>
        <button type="submit" class="btn btn-lg btn-default btn-block  btn-no" name="no" id="nobutton">
        <?php echo htmlspecialchars($this->t('{consent:consent:no}')) ?>
        </button>
    </form>


</div>
</div>
<?php

$this->includeAtTemplateBase('includes/footer.php');
