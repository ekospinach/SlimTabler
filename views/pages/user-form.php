<?php
// Edit user page (rendered inside dashboard layout)
$user = $user ?? ['id' => '', 'name' => '', 'email' => ''];
$formError = $formError ?? '';
?>
<div class="row row-deck row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit User</h3>
            </div>
            <div class="card-body">
                <?php if ($formError): ?>
                    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($formError) ?></div>
                <?php endif; ?>
                <form method="post" action="/users/<?= $user['id'] ?>/update">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password <span class="form-help">Leave blank to keep current</span></label>
                        <input type="password" name="password" class="form-control" minlength="6">
                    </div>
                    <div class="form-footer">
                        <a href="/users" class="btn btn-link link-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary ms-auto">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
