<div class="mail-option">
	<div class="btn-group">
		<a href="#" title="Reload messages" class="btn mini tooltips btn-reload">
			<i class=" fa fa-refresh"></i>
		</a>
	</div>
	<div class="btn-group hidden-phone">
		<a data-toggle="dropdown" href="#" class="btn mini blue" aria-expanded="false">
			Action...
			<i class="fa fa-angle-down "></i>
		</a>
		<ul class="dropdown-menu">
			<li id="markAsRead"><a href="#"><i class="fa fa-pencil"></i> Mark as Read</a></li>
			<li id="markAsSpam"><a href="#"><i class="fa fa-ban"></i> Spam</a></li>
			<li class="divider"></li>
			<li id="deleteMessages"><a href="#"><i class="fa fa-trash-o"></i> Delete</a></li>
		</ul>
	</div>

	<ul class="unstyled inbox-pagination">
		<li><span class="page-info"><?= "$first-$last of $total_items" ?></span></li>
		<?php
			$prev_visibility = ($first == 1)? 'style="visibility: hidden;"':'';
			$next_visibility = ($last == $total_items)? 'style="visibility: hidden;"':'';
		?>
		<li <?= $prev_visibility; ?>>
			<a data-page="<?= $page - 1 ?>" class="np-btn page-previous"><i class="fa fa-angle-left  pagination-left"></i></a>
		</li>
		<li <?= $next_visibility; ?>>
			<a data-page="<?= $page + 1 ?>" class="np-btn page-next"><i class="fa fa-angle-right pagination-right"></i></a>
		</li>
	</ul>
</div>

<table id="messagesTable" class="table table-inbox table-hover">
	<tbody>
	<?php foreach($messages as $message): ?>
		<tr data-msg-id="<?= $message['uid']; ?>" <?= ($message['is_seen'])? '':'class="unread"'; ?>>
			<td class="inbox-small-cells">
				<input type="checkbox" class="mail-checkbox">
			</td>
			<td class="view-message"><?= $message['subject']; ?></td>
			<td class="view-message "><?= limit_text($message['body'], 10).'...'; ?></td>
			<td class="view-message  inbox-small-cells"><?= ($message['atchCount'] > 0)? '<i class="fa fa-paperclip"></i>':''; ?></td>
			<td class="view-message text-right"><?= $message['date']; ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
