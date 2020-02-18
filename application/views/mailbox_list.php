<ul class="inbox-nav inbox-divider">
	<?php foreach($mailboxes as $key => $mailbox): ?>
		<?php
			$mb_name = ($mailbox['name'] == 'INBOX')? 'Inbox':substr($mailbox['name'], 6);
			$is_active = ($key == 0)? 'active':'';
			$unseen_cnt_hidden = ($mailbox['status']['unseen'] == 0)? 'hidden':'';
		?>
		<li class="mailbox-item <?= $is_active; ?>" data-mailbox-enc-name="<?= $mailbox['name']; ?>">
			<a><i class="fa fa-inbox"></i> <span class="mb-name"><?= $mb_name;?></span> <span class="label label-danger pull-right unseen-count <?= $unseen_cnt_hidden; ?>"><?=$mailbox['status']['unseen'];?></span ></a>
		</li>
	<?php endforeach; ?>
</ul>
