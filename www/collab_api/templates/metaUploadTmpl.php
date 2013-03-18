<? require 'header_v1.php' ?>
<div class="container">
<h2>META</h2>
    <ul>
        <li><?=$fileName?></li>
        <li><?=$fileMime?></li>
        <li><?=$fileExtension?></li>
        <li><?=$fileSize?></li>
    </ul>
    <?if (!empty($errors)):?>
    <div class="alert alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Attention! Voici les erreurs survenues lors du téléchargement:</strong>
        <ul>
            <?php
                foreach($errors as $msg){
                    echo " <li>$msg</li>";
                }
            ?>
        </ul>
    </div>
    <?endif;?>

    <form class="form-horizontal">
        <div class="control-group">
            <label class="control-label" for="inputEmail">Nom</label>
            <div class="controls">
                <input class="span6" type="text" id="inputEmail" placeholder="Nom">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="description">Description</label>
            <div class="controls">
                <textarea class="span6" name="description" id="description" cols="30" rows="4" placeholder="Description"></textarea>
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                <label class="checkbox">
                    <input type="checkbox"> Remember me
                </label>
                <button type="submit" class="btn">Sign in</button>
            </div>
        </div>
    </form>

    <div class="quick-look">
        <pre id="prettyJSON"></pre>
    </div>
</div>

<script type="text/javascript">
    var formattedJSON = JSON.stringify(<?=$data?>, null, 4);
    $("#prettyJSON").html(formattedJSON);
</script>

<? require 'footer_v1.php' ?>