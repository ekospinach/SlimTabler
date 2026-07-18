<!-- Tables Page -->

<!-- Employee Table Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Employee Directory</h3>
        <div class="card-actions">
            <div class="input-icon">
                <span class="input-icon-addon">
                    <i class="ti ti-search"></i>
                </span>
                <input type="text" class="form-control form-control-sm" placeholder="Search employees…">
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped">
            <thead>
                <tr>
                    <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" aria-label="Select all"></th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Salary</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $employees = [
                    ['John Doe', 'Engineering', 'Senior Developer', 'active', '$95,000'],
                    ['Jane Smith', 'Marketing', 'Marketing Manager', 'active', '$72,000'],
                    ['Mike Johnson', 'Sales', 'Sales Rep', 'active', '$55,000'],
                    ['Sara Wilson', 'Design', 'UI/UX Designer', 'active', '$78,000'],
                    ['Alex Brown', 'Engineering', 'Backend Developer', 'inactive', '$88,000'],
                    ['Emily Davis', 'HR', 'HR Coordinator', 'active', '$52,000'],
                    ['Chris Lee', 'Finance', 'Accountant', 'active', '$62,000'],
                    ['Lisa Anderson', 'Engineering', 'DevOps Engineer', 'active', '$105,000'],
                    ['Tom Martinez', 'Support', 'Support Lead', 'inactive', '$48,000'],
                    ['Rachel Taylor', 'Product', 'Product Manager', 'active', '$92,000'],
                ];
                foreach ($employees as $emp):
                ?>
                <tr>
                    <td><input class="form-check-input m-0 align-middle" type="checkbox" aria-label="Select employee"></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm" style="background-image: url(https://ui-avatars.com/api/?name=<?= urlencode($emp[0]) ?>&background=206bc4&color=fff)"></span>
                            <strong><?= htmlspecialchars($emp[0]) ?></strong>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($emp[1]) ?></td>
                    <td><?= htmlspecialchars($emp[2]) ?></td>
                    <td>
                        <?php if ($emp[3] === 'active'): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($emp[4]) ?></td>
                    <td>
                        <div class="btn-list btn-list-end">
                            <a href="#" class="btn btn-sm btn-outline-primary">Edit</a>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#"><i class="ti ti-eye me-2"></i> View</a>
                                    <a class="dropdown-item" href="#"><i class="ti ti-edit me-2"></i> Edit</a>
                                    <a class="dropdown-item text-danger" href="#"><i class="ti ti-trash me-2"></i> Delete</a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-muted">Showing <strong>1</strong> to <strong>10</strong> of <strong>10</strong> entries</p>
        <ul class="pagination m-0 ms-auto">
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                    <i class="ti ti-chevron-left"></i> Prev
                </a>
            </li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
                <a class="page-link" href="#">
                    Next <i class="ti ti-chevron-right"></i>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Invoices Table -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Invoices</h3>
        <div class="card-actions">
            <a href="#" class="btn btn-primary btn-sm">
                <i class="ti ti-plus me-1"></i> New Invoice
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Client</th>
                    <th>Issued</th>
                    <th>Due</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $invoices = [
                    ['INV-001', 'Acme Corp', '2026-07-01', '2026-07-15', '$2,500', 'paid'],
                    ['INV-002', 'Globex Inc', '2026-07-02', '2026-07-16', '$4,800', 'pending'],
                    ['INV-003', 'Initech', '2026-07-03', '2026-07-17', '$1,200', 'overdue'],
                    ['INV-004', 'Umbrella Co', '2026-07-05', '2026-07-19', '$6,300', 'paid'],
                    ['INV-005', 'Stark Ind', '2026-07-06', '2026-07-20', '$3,400', 'pending'],
                ];
                foreach ($invoices as $inv):
                    $statusClass = match($inv[5]) {
                        'paid' => 'bg-success',
                        'pending' => 'bg-warning',
                        'overdue' => 'bg-danger',
                        default => 'bg-secondary',
                    };
                    $statusLabel = ucfirst($inv[5]);
                ?>
                <tr>
                    <td><strong><?= $inv[0] ?></strong></td>
                    <td><?= $inv[1] ?></td>
                    <td><?= $inv[2] ?></td>
                    <td><?= $inv[3] ?></td>
                    <td><?= $inv[4] ?></td>
                    <td><span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                    <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
