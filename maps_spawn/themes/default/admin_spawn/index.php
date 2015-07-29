<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<?php if (!empty($successMessage)): ?>
<p class="green"><?php echo htmlspecialchars($successMessage) ?></p>
<?php endif ?>
<h2>Tables with monsters spawn and maps</h2>

<?php if($MobSpawnBase === false){ ?>
    Not found table `mob_spawn`
<?php } else { ?>
    <b id="monNum"><?=$MobSpawnBase?></b> Mobs in database
<?php } ?><br>
<?php if($mapIndexBase === false){ ?>
    Not found table `map_index`
<?php } else { ?>
    <b id="mapNum"><?=$mapIndexBase?></b> Maps in database
<?php } ?>

<h3>Mysql Tables</h3>

<script>
    $(document).ready(function(){
        $('.complete').submit(function(){
            return confirm("Are you sure?");
        });
    });
</script>

<form class="complete" method="POST">
    <input type="hidden" name="act" value="create" />
    <input class="btn" type="submit" value="Create" />
</form>
<form class="complete" method="POST">
    <input type="hidden" name="act" value="truncate" />
    <input class="btn" type="submit" value="Clean" />
</form>
<form class="complete" method="POST">
    <input type="hidden" name="act" value="delete" />
    <input class="btn" type="submit" value="Delete" />
</form>

<hr />

<h3>Upload data</h3>

<form class="forms" method="post" enctype="multipart/form-data">
    Upload ZIP archive with monter tables<br>
    <b>Example:</b> <br>Add to archive folder <b>*Athena/npc/re/mobs</b> and upload it<br>
    <input type="file" name="mobs_zip"><br>
    <input class="btn" type="submit">
</form>

<form class="forms" method="post" enctype="multipart/form-data">
    Upload file  <b>*Athena/db/map_index.txt</b><br>
    <input type="file" name="map_index"><br>
    <input class="btn" type="submit">
</form>
<?php if(sizeof($file)){ ?>
    <script>
        $(document).ready(function() {
            var FILES = ['<?=join('\',\'', $file)?>'];
            var COUNT = 0;
            var errors = [];
            $('.loading').show();
            startInsert();
            function startInsert() {
                if (errors[COUNT] >= 3) {
                    COUNT++;
                }
                if (typeof errors[COUNT] === 'undefined') {
                    errors[COUNT] = 0;
                }
                if (COUNT >= FILES.length) {
                    $('.loading').hide();
                    $.get('?module=admin_spawn&action=get&type=delDir');
                    return;
                }
                console.log(FILES[COUNT])
                $.ajax({
                    type: 'POST',
                    url: '?module=admin_spawn&action=get',
                    dataType: 'json',
                    data: {
                        file_name: FILES[COUNT]
                    },
                    success: function (data) {
                        $('#monNum').text(parseInt($('#monNum').text()) + parseInt(data.total));
                        $('.table').append('<tr><td>Файл <b>' + data.file + '</b> Успешно загружен. Монстров импортировано <b>' + data.total + '</b></td></tr>');
                        COUNT++;
                        startInsert();
                    },
                    error: function () {
                        errors[COUNT]++;
                        $('.table').append('<tr><td class="reds">Файл <b>' + FILES[COUNT] + '</b> Не удалось обработать (' + errors[COUNT] + ' попытка из 3х)</td></tr>');
                        startInsert();
                    }
                })
            }
        });
    </script>
<?php } ?>

<h3>Base Configuration</h3>
<form class="forms" method="post" enctype="multipart/form-data">
    Renewal/Pre-Renewal<br>
    <input type="hidden" name="setConfig" value="1">
    <select class="btn" name="renewal">
        <option value="REN" <?=$MAIN_CONFIG['renewal'] == 'REN' ? 'selected' : ''?>>Renewal</option>
        <option value="PRE" <?=$MAIN_CONFIG['renewal'] == 'PRE' ? 'selected' : ''?>>Pre-Renewal</option>
    </select>
    <input class="btn" type="submit">
</form>

<div class="loading"><img src="/addons/maps_spawn/themes/default/admin_spawn/load.gif"> Загружаю . . .</div>
<table class="table">
</table>
<div class="loading"><img src="/addons/maps_spawn/themes/default/admin_spawn/load.gif"> Загружаю . . .</div>


<style>
    .forms{display:inline-block;width:400px;}
    .btn{width:200px;height:30px;margin:10px;}
    .loading{padding: 20px;text-align: center;display:none;}
    .table{width: 100%;border-spacing:0}
    .table td{background-color: #93ff87}
    .table td.reds{background-color: #ff9b97}
</style>