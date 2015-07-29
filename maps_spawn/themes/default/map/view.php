<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<?php if (!empty($successMessage)): ?>
    <p class="green"><?php echo htmlspecialchars($successMessage) ?></p>
<?php endif ?>
<h2>Map Database</h2>
<a style="display:block;" href="<?=$this->url('map')?>">Back to Map Database</a>
<?php if($map){ ?>
    <h3>Map "<b><?=$params->get('map')?></b>"</h3>
    <img src="<?=mapImage($params->get('map'))?>" style="display:block;">
    <?php if(sizeof($mobs)){ ?>
        <table class="vertical-table">
            <tr>
                <th>Mob Name</th>
                <th>Spawn</th>
                <th>Respawn time</th>
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
                </tr>
            <?php } ?>
        </table>
    <?php }else{ ?>
        No monster on this map.
    <?php } ?>
<?php }else{ ?>
    No area found
<?php } ?>