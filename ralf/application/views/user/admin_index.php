<script type="text/javascript">

$(document).ready(function() {
    $("#datatable").dataTable({
        "oLanguage": {
            "sUrl": "<?php echo Kohana::$config->load("sitio.media");?>vendor/datatable/language/spanish.txt"
        },
        "aaSorting": [[ 2, "asc" ]]
    });
    $(".dataTable_hide").hide();
    
    $('.fancybox_edit_user').live('click', function () {
        $this = $(this);
        $.fancybox({
            'href': $this.attr('href'),
            'width': 600,
            'height': 300,
            'padding': 0,
            'centerOnScroll': true,
            'type': 'iframe',
            'onClosed': function() {   
                parent.location.reload(true); 
            ;}
        });
        return false;
    });
    
    $('.fancybox_add_user').live('click', function () {
        $this = $(this);
        $.fancybox({
            'href': 'user/add',
            'width': 600,
            'height': 400,
            'padding': 0,
            'centerOnScroll': true,
            'type': 'iframe',
            'onClosed': function() {   
                parent.location.reload(true); 
            ;}
        });
        return false;
    });
});

</script>

<h2 class="tit-bandeja"><?php echo __('Usuarios'); ?>
    <div align="right">
        <?php echo Form::button('add_user', '+ Nuevo Usuario', array('class'=>'boton fancybox_add_user')); ?>
    </div>
</h2>

<?php if(count($users)>0): ?>
<table id="datatable" class="tabla-general">
    <thead>
        <th class="dataTable_hide"><?php echo __("ID"); ?></th>
        <th>Nombre</th>
        <th>Usuario</th>
        <th>Email</th>
        <th>Región</th>
        <th>Roles</th>
        <th>Editar</th>
    </thead>
    <tbody>
        <?php foreach ($users as $user) {
            $email = explode('+', $user->email);
            $rol = ORM::factory("RolUser")
                    ->select('roles.name')
                    ->join('roles', 'LEFT')->on('roles.id', '=', 'roluser.role_id' )
                    ->where('user_id','=',$user->id)->find_all();
            //echo Database::instance()->last_query;die();
            $roles = "";
            foreach ($rol as $r) {
                $roles .= $r->name . ", ";
            }
            $roles = trim($roles, ', ');
            ?>
            <tr>
                <td class="dataTable_hide"><?php echo $user->id; ?></td>
                <td><?php echo $user->name; ?></td>
                <td><?php echo $user->username; ?></td>
                <td><?php echo $user->email; ?></td>
                <td><?php echo $user->nombre; ?></td>
                <td><?php echo $roles; ?></td>
                <td><?php echo Html::anchor("user/edit/{$user->id}", 'Editar', array('class'=>'fancybox_edit_user')); ?></td>
            </tr>
        <?php } ?>
    </tbody>
<?php else: ?>
    <h3 class="information"><?php echo __("No hay información disponible");?></h3>
<?php endif;?>
</table>