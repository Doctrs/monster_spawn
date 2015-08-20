<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<?php if (!empty($successMessage)): ?>
    <p class="green"><?php echo htmlspecialchars($successMessage) ?></p>
<?php endif ?>

<style>
    .points{position:absolute;border:3px double #ff00ff;}
    .points_npcs{position:absolute;width:10px;height:10px;background-color: green;border:3px solid #ffff00}
    .hide{display:none;}
    .you_here{z-index:9999;position:absolute;width:10px;height:10px;background-color: green;border:2px solid yellow;border-radius:20px;}
    .warps:hover{border-color:black;cursor:pointer;background-color: #ff443f  }
    .warps{position:absolute;width:20px;height:20px;background-color: red;border:2px solid yellow;border-radius:20px;}
    .tab{
        padding:10px;
        border:1px solid #E1EAF3;
        display: inline-block;
        border-radius: 10px;
        cursor:pointer;
    }
    .tabs_un{
        height:500px;
    }
    .tab.active{
        background-color: #8EBCEB;
    }
</style>
<script>
    $(document).ready(function(){
        $('.npcs_hover').hover(function(){
            $('.' + $(this).attr('data')).show();
        }, function(){
            $('.' + $(this).attr('data')).hide();
        });
        $('.tab').on('click', function(){
            $('.tab').each(function(){
                $('#' + $(this).attr('data')).hide();
                $(this).removeClass('active');
            })
            $('#' + $(this).attr('data')).show();
            $(this).addClass('active');
        });
    })
</script>
<h2>Map Database</h2>
<a style="display:block;" href="<?=$this->url('map')?>">Back to Map Database</a>

<?php if($map){ ?>

<table style="display:inline-block" class="vertical-table" style="margin-top:10px;">
    <tr>
        <td>
            <h3>Map "<b><?=$map->name?></b>"</h3>
            <div style="display:inline-block;position:relative;width:512px;height:512px">
                <img src="<?=mapImage($map->name)?>" style="width:100%;height:100%;">

                <?php if((int)$params->get('x') && (int)$params->get('y')){ ?>
                    <div class="you_here" style="
                        left:<?=conv((int)$params->get('x'), $map->x, $map) - 5?>px;
                        bottom:<?=conv((int)$params->get('y'), $map->y, $map) - 5?>px;
                        "></div>
                <?php } ?>

                <?php foreach($npsc as $npc){?>

                    <div class="npc_<?=$npc->x?>-<?=$npc->y?> points_npcs hide npc_resp" style="
                        left:<?=conv($npc->x, $map->x, $map)?>px;
                        bottom:<?=conv($npc->y, $map->y, $map)?>px;
                        "></div>

                <?php } ?>
                <?php foreach($shops as $shop){?>

                    <div class="npc_<?=$shop->x?>-<?=$shop->y?> points_npcs hide npc_resp" style="
                        left:<?=conv($shop->x, $map->x, $map)?>px;
                        bottom:<?=conv($shop->y, $map->y, $map)?>px;
                        "></div>

                <?php } ?>

                <?php $isResp = false; foreach($mobs as $mob){ if(!$mob->x){continue;} $isResp = true; ?>

                    <div class="mob_spawn_<?=$mob->id?> points hide mob_resp" style="
                        width:<?=conv($mob->range_x, $map->x)?>px;
                        height:<?=conv($mob->range_y, $map->y)?>px;
                        left:<?=conv($mob->x, $map->x, $map) - conv($mob->range_x, $map->x, $map) / 2?>px;
                        bottom:<?=conv($mob->y, $map->y, $map) - conv($mob->range_y, $map->y, $map) / 2?>px;
                        "></div>

                <?php } ?>


                <?php foreach($warps as $warp){?>

                    <a href="<?=$this->url('map', 'view')?>&map=<?=$warp->to?>&x=<?=$warp->tx?>&y=<?=$warp->ty?>">
                    <div class="warps" style="
                        left:<?=conv($warp->x, $map->x, $map) - 10?>px;
                        bottom:<?=conv($warp->y, $map->y, $map) - 10?>px;
                        "></div></a>

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
            <div>
                <div class="tab active" data="mobs_table">Mobs</div>
                <div class="tab" data="npcs_table">NPCs</div>
                <div class="tab" data="shops_table">Shops</div>
            </div>
            <div id="mobs_table" class="tabs_un">
                <?php if(sizeof($mobs)){ ?>
                    <table style="max-height: 500px;overflow: auto;display:inline-block" class="vertical-table" style="margin-top:10px;">
                        <tr>
                            <th>Mob Name</th>
                            <th>Spawn</th>
                            <th>Respawn time</th>
                            <?php if($isResp){ ?>
                                <th>Respawn Area</th>
                            <?php } ?>
                        </tr>
                        <tbody>
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
            </div>

            <div id="npcs_table" class="tabs_un" style="display:none;">
                <?php if(sizeof($npsc)){ ?>
                    <table style="max-height: 500px;overflow: auto;display:inline-block" class="vertical-table" style="margin-top:10px;">
                        <tr>
                            <th>NPC Name</th>
                            <th>Image</th>
                            <th>Coordinates</th>
                        </tr>
                        <tbody>
                        <?php foreach($npsc as $npc){?>
                            <tr class="npcs_hover" data="npc_<?=$npc->x?>-<?=$npc->y?>">
                                <td><?=$npc->name?></td>
                                <td><img src="<?=npcImage($npc->sprite)?>" /></td>
                                <td><?=$npc->x . ',' . $npc->y?></td>
                            </tr>
                        <?php } ?>
                        </tbody>

                    </table>

                <?php }else{ ?>

                    No NPSc on this map.

                <?php } ?>
            </div>

            <div id="shops_table" class="tabs_un" style="display:none;">
                <?php if(sizeof($shops)){ ?>
                    <table style="max-height: 500px;overflow: auto;display:inline-block" class="vertical-table" style="margin-top:10px;">
                        <tr>
                            <th>Shop Name</th>
                            <th>Image</th>
                            <th>Coordinates</th>
                        </tr>
                        <tbody>
                        <?php foreach($shops as $shop){?>
                            <tr class="npcs_hover" data="npc_<?=$shop->x?>-<?=$shop->y?>">
                                <td><?=$shop->name?></td>
                                <td><img src="<?=npcImage($shop->sprite)?>" /></td>
                                <td><?=$shop->x . ',' . $shop->y?></td>
                            </tr>
                        <?php } ?>
                        </tbody>

                    </table>

                <?php }else{ ?>

                    No Shops on this map.

                <?php } ?>
            </div>

            <?php }else{ ?>

                No area found

            <?php } ?>
        </td></tr></table>