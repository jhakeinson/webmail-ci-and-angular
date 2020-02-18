
<div class="mail-option">
	<div class="btn-group hidden-phone">
		<button class="btn mini blue btn-reply" aria-expanded="false">
			Reply
		</button>
	</div>
	<div class="btn-group hidden-phone">
		<button class="btn mini blue btn-forward" aria-expanded="false">
			Forward
		</button>
	</div>
	<div class="btn-group hidden-phone">
		<button class="btn mini blue btn-delete" aria-expanded="false">
			Delete
		</button>
	</div>
</div>

<div data-msg-id="<?=$message['uid'] ?>" class="panel panel-info message">
	<div class="panel-header">
		<table class="table">
			<tr>
				<td class="row-title"><strong>From:</strong></td>
				<td><span class="from"><?= $message['from']; ?></span></td>
			</tr>
			<tr>
				<td class="row-title"><strong>To:</strong></td>
				<td><span class="to"><?= join('; ', $message['to']); ?></span></td>
			</tr>
			<tr>
				<td class="row-title"><strong>Date:</strong></td>
				<td><?= $message['date'] ?></td>
			</tr>
			<tr>
				<td class="row-title"><strong>Subject:</strong></td>
				<td><span class="subject"><?= $message['subject'] ?></span></td>
			</tr>
			<tr>
				<td class="row-title"><strong>Attachments:</strong></td>
				<td>
					<?php foreach($message['attachments'] as $atch): ?>
						<span class="badge attachment-badge">
							<a target="_blank" href="<?=$atch['url']?>"><?=$atch['filename']?></a>
						</span>
					<?php endforeach; ?>
				</td>
			</tr>
		</table>
	</div>
	<div class="panel-body">
		<div class="container-fluid body">
			<?= $message['body']; ?>
		</div>
	</div>
</div>
