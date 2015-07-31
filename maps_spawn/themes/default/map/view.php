<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<?php if (!empty($successMessage)): ?>
    <p class="green"><?php echo htmlspecialchars($successMessage) ?></p>
<?php endif ?>

<style>
    .points{position:absolute;border:3px double #ff00ff;}
    .not_walk{position:absolute;background-color: red}
    .hide{display:none;}
</style>
<h2>Map Database</h2>
<a style="display:block;" href="<?=$this->url('map')?>">Back to Map Database</a>

<?php if($map){?>
<?php if($map->cell_data){ ?>
<script>
    $(document).ready(function() {
        var canvas = document.getElementById("canvas");
        var ctx = canvas.getContext("2d");
        canvas.width = 512;
        canvas.height = 512;
        <?php for($i = 0 ; $i < strlen($map->cell_data) ; $i ++) {
        $color = $map->cell_data[$i];
        if($color == 0 || $color > 1 && $color < 9){
            echo 'ctx.globalAlpha=1;ctx.fillStyle = "#' . $color . '9' . $color . '9' . $color . '9";';
        } elseif($color == 9){
            echo 'ctx.globalAlpha=0;';
        } else {
            echo 'ctx.globalAlpha=1;ctx.fillStyle = "#999";';
        }
        ?>
        ctx.fillRect(<?=conv($i % $map->x, $map->x)?>,<?=512 - conv(ceil($i / $map->y), $map->y)?>,<?=(conv(1, $map->x))?>, <?=(conv(1, $map->y))?>);
        <?php } if(!mapImage($map->name)){ ?>
        $.ajax({
            type: "POST",
            url: window.location.href,
            dataType: 'text',
            data: {
                image : canvas.toDataURL(),
                mn: '<?=$map->name?>'
            }, success: function(data){
                console.log(data);
            }
        });
        <?php } ?>
    });

</script>
<?php } ?>


<table style="display:inline-block" class="vertical-table" style="margin-top:10px;">
    <tr>
        <td>
            <h3>Map "<b><?=$map->name?></b>"</h3>
            <div style="background-image: url(<?=mapImage($map->name)?>);display:inline-block;position:relative;width:512px;height:512px">
                <canvas id="canvas"></canvas>

                <?php $isResp = false; foreach($mobs as $mob){ if(!$mob->x){continue;} $isResp = true; ?>

                    <div class="mob_spawn_<?=$mob->id?> points hide mob_resp" style="
                        width:<?=conv($mob->range_x, $map->x)?>px;
                        height:<?=conv($mob->range_y, $map->y)?>px;
                        left:<?=conv($mob->x, $map->x) - conv($mob->range_x, $map->x) / 2?>px;
                        bottom:<?=conv($mob->y, $map->y) - conv($mob->range_y, $map->y) / 2?>px;
                        "></div>

                <?php } ?>
                <?php for($i = 0 ; $i < strlen($map->cell_data) ; $i ++) { continue; if(!$map->cell_data[$i]){continue;} ?>

                    <div class="not_walk" style="
                        width:<?=conv(1, $map->x)?>px;
                        height:<?=conv(1, $map->y)?>px;
                        left:<?=conv($i % $map->x - 1, $map->x)?>px;
                        bottom:<?=conv(floor($i / $map->y), $map->y)?>px;
                        "></div>

                <?php } ?>


            </div>
            <?php if($isResp){ ?>
                <div style="padding:20px;text-align:center;">
                    <button onclick="$('.points').removeClass('hide')">Show all monster respawn</button>
                    <button onclick="$('.points').addClass('hide')">Hide all monster respawn</button>
                </div>
            <?php } ?>
        </td>
        <td>
            <?php if(sizeof($mobs)){ ?>
                <table style="display:inline-block" class="vertical-table" style="margin-top:10px;">
                    <tr>
                        <th>Mob Name</th>
                        <th>Spawn</th>
                        <th>Respawn time</th>
                        <?php if($isResp){ ?>
                            <th>Respawn Area</th>
                        <?php } ?>
                    </tr>

                    <?php foreach($mobs as $mob){ ?>

                        <tr>
                            <td><a href="<?=$this->url('monster_new', 'view')?>&id=<?=$mob->mob_id?>"><?=$mob->name?></td>
                            <td><?=$mob->count?></td>
                            <td><b><?=ceil($mob->time_to / 60000)?></b>min<?=
                                ($mob->time_from ?
                                    '-<b>' . (ceil($mob->time_to / 60000) + ceil($mob->time_from / 60000)) . '</b>min' :
                                    '')
                                ?></td>
                            <?php if($isResp){ ?>
                                <td align="center">
                                    <?php if($mob->x){ ?>
                                        <button onclick="$('.mob_spawn_<?=$mob->id?>').toggleClass('hide')" class="mob_spawn_<?=$mob->id?>">Show</button>
                                        <button onclick="$('.mob_spawn_<?=$mob->id?>').toggleClass('hide')" class="mob_spawn_<?=$mob->id?> hide">Hide</button>
                                    <?php } ?>
                                </td>
                            <?php } ?>
                        </tr>

                    <?php } ?>

                </table>

            <?php }else{ ?>

                No monster on this map.

            <?php } ?>

            <?php }else{ ?>

                No area found

            <?php } ?>
        </td></tr></table>