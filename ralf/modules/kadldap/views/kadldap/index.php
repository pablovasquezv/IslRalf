<div style="margin-left:40px">

<h2>Kadldap Configuration &amp; Connection Test</h2>
<p>Here you can test your Kadldap configuration.</p>

<?php if ($message) echo '<p class="note">'.$message.'</p>' ?>

<?php if (!Auth::instance()->logged_in()): ?>
<script type="text/javascript">
	$().ready(function() {
		$('input[name="username"]').focus();
	});
</script>

<?php echo Form::open() ?>
<p>
	<?php echo Form::label('username', 'Username:')?>
	<?php echo Form::input('username', '', array('id'=>'focus-me')) ?>
	<code><?php echo $kadldap->getAccountSuffix() ?></code>
</p>
<p>
	<?php echo Form::label('password', 'Password:')?>
	<?php echo Form::password('password') ?>
</p>
<p>
	<?php echo Form::submit('login', 'Login') ?>
</p>
<?php echo Form::close() ?>

<?php else: // if (!Auth::instance()->logged_in()): ?>

<p>
	You are logged in as
	<strong><?php echo Auth::instance()->get_user() ?></strong>
	and have the following roles:
	<?php if (count(Auth::instance()->get_roles()) > 0): ?>
	<strong><?php echo join('</strong>, <strong>', Auth::instance()->get_roles()) ?></strong>.
	<?php else: ?>
	<em>No roles found</em>.
	<?php endif ?>
</p>
<p>
	<?php echo html::anchor('kadldap/logout', '[Log out]') ?>
</p>

<h2>User Values</h2>
<table>
	<?php foreach ($userinfo as $label => $info) {
		if (!is_array($info)) continue;
	?>
	<tr>
		<th rowspan="<?php echo $info['count'] ?>"><?php echo $label ?></th>
		<td><?php 
			if ($info['count']==1)
			{
				echo $info[0];
			} else
			{
				unset($info['count']);
				echo join ('</td></tr><tr><td>', $info);
			}
			?>
		</td>
	</tr>
	<?php } // foreach ($userinfo as $label => $info) ?>
</table>
<?php endif ?>

</div>
