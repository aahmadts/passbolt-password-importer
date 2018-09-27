<?php
?>
<div class="container" style="margin: 25px; max-width: 600px;">
    <div>
        <h1 class="warning">Please read!</h1>
        <p>
            Browse to the .csv file to import resources/passwords to your account.
            Existing records (created by you or by others) will NOT be imported if they have the same url and username and password.
            <br>
            The <code>cvs</code> file MUST have the following values order to be import correctly:
            <p>
                <code>url, username, password, description, name/title </code>
            </p>
        Any other values in the file will be ignored!
        <br>
        <br>
            <p class="text-danger">* Please make sure that descriptions contain no line breaks or import them manually!</p>
        </p>
    </div>
    <div>
        <form action="/password-importer/import" id="import_form" style="padding: 50px 0;" method="post" enctype="multipart/form-data">
            <div class="form-group" style="border: solid aliceblue 1px; padding: 5px;">
                    <label for="password_file">Select file to import passwords from</label>
                    <input type="file" name="passwordFile"  class="form-control-file" />
            </div>
            <div>
                <?php
                if (isset($this->viewVars['allGroups']) && count($this->viewVars['allGroups']) > 0 ) {
                    ?>
                    <h5>Share:</h5>
                    <label>choose the group you want to share the passwords with:</label>
                    <select id="group" name="group">
                        <option value="">Select a group</option>
                        <? foreach ($this->viewVars['allGroups'] as $group)  { ?>
                            <option value="<?= $group->id ?>"><?= $group->name ?></option>
                        <? } ?>
                    </select>
                    <?php
                } else {
                ?>
                  <i>You have no active groups to share secrets with.</i>
                <?php
                }
                ?>
            </div>
            <div style="margin-top: 15px;text-align: center">
                <input id="startImport" type="submit" class="btn btn-primary" style="width:100%;" value="import">
            </div>

        </form>
    </div>
    <div>
        <div id="myResultsDiv" class="alert alert-info" style="display: none;" role="alert">

        </div>
    </div>

</div>
