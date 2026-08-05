<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ========================= -->
    <!-- Page Metadata & Assets -->
    <!-- ========================= -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Asset — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/form.css">
</head>
<body>
<!-- ========================= -->
<!-- Edit User Page -->
<!-- ========================= -->
<div class="edit-container<?php echo (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN') ? ' admin-edit' : ''; ?>">
    <div class="card">
        <!-- ========================= -->
        <!-- Card Header -->
        <!-- ========================= -->
        <div class="card-header">
            <div class="icon">
                <?php if (!empty($assetData['profile_image'])): ?>
                    <img src="../storage/profile_images/<?= htmlspecialchars($assetData['profile_image']) ?>"
                         alt="Profile image">
                <?php else: ?>
                    <svg viewBox="0 0 24 24">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/>
                    </svg>
                <?php endif; ?>
            </div>
            <h1>Add New Asset</h1>
        </div>
        <!-- ========================= -->
        <!-- General Validation Error -->
        <!-- ========================= -->
        <?php if (!empty($errors['general'])): ?>
            <div class="alert-error">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <?php echo htmlspecialchars($errors['general']); ?>
            </div>
        <?php endif; ?>

        <!-- ========================= -->
        <!-- Create asset Form -->
        <!-- ========================= -->

        <form action="index.php?route=assets/create" method="post">
            <div class="form-grid">

                <!-- ========================= -->
                <!-- Name Field -->
                <!-- ========================= -->
                <div class="form-group  <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                    <label for="name">Asset Name <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="name" id="name" placeholder="ex. Dell OptiPlex 7010"
                               value="<?php echo htmlspecialchars($old['name'] ?? $assetData['name'] ?? ''); ?>">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M20 7 12 3 4 7v10l8 4 8-4V7z"/>
                            <path d="M12 21V11"/>
                            <path d="M4 7l8 4 8-4"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['name'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($errors['name']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Serial Number Field -->
                <!-- ========================= -->
                <div class="form-group  <?php echo isset($errors['serial_number']) ? 'has-error' : ''; ?>">
                    <label for="name">Serial Number <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" id="serial_number" name="serial_number" placeholder="ex. DOPT7010SN15"
                               value="<?= htmlspecialchars($old['serial_number'] ?? $assetData['serial_number'] ?? '') ?>">

                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M20 7 12 3 4 7v10l8 4 8-4V7z"/>
                            <path d="M12 21V11"/>
                            <path d="M4 7l8 4 8-4"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['serial_number'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($errors['serial_number']); ?>
                        </div>
                    <?php endif; ?>
                </div>


                <!-- ========================= -->
                <!-- Brand Field -->
                <!-- ========================= -->
                <div class="form-group <?php echo isset($errors['brand']) ? 'has-error' : ''; ?>">
                    <label for="email">Brand <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="brand" id="brand" placeholder="ex. Dell"
                               value="<?php echo htmlspecialchars($old['brand'] ?? $assetData['brand'] ?? ''); ?>">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="6"/>
                            <path d="M8.5 14 7 22l5-3 5 3-1.5-8"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['brand'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($errors['brand']); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- ========================= -->
                <!-- Model Field -->
                <!-- ========================= -->
                <div class="form-group <?php echo isset($errors['model']) ? 'has-error' : ''; ?>">
                    <label for="model">Model <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="model" id="model" placeholder="ex. OptiPlex 7010"
                               value="<?php echo htmlspecialchars($old['model'] ?? $assetData['model'] ?? ''); ?>">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <rect x="7" y="7" width="10" height="10" rx="1"/>
                            <path d="M9 1v3M15 1v3M9 20v3M15 20v3"/>
                            <path d="M20 9h3M20 15h3M1 9h3M1 15h3"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['model'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg><?php echo htmlspecialchars($errors['model']); ?></div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Category Selection -->
                <!-- ========================= -->
                <div class="form-group <?php echo isset($errors['category_id']) ? 'has-error' : ''; ?>">
                    <label for="category_id">Category <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select id="category_id" name="category_id">
                            <option value="">Select Category</option>
                            <?php $selectedCategory = (int)($assetData['category_id'] ?? 0); ?>
                            <?php foreach ($categories ?? [] as $category): ?>
                                <option value="<?= (int)$category['id'] ?>" <?= $selectedCategory === (int)$category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['category_id'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg><?php echo htmlspecialchars($errors['category_id']); ?></div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Vendor Selection -->
                <!-- ========================= -->
                <div class="form-group <?php echo isset($errors['vendor_id']) ? 'has-error' : ''; ?>">
                    <label for="vendor_id">Vendor <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select id="vendor_id" name="vendor_id">
                            <option value="">Select Vendor</option>
                            <?php $selectedVendor = (int)($assetData['vendor_id'] ?? 0); ?>
                            <?php foreach ($vendors ?? [] as $vendor): ?>
                                <option value="<?= (int)$vendor['id'] ?>" <?= $selectedVendor === (int)$vendor['id'] ? 'selected' : '' ?>><?= htmlspecialchars($vendor['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M3 9l1-5h16l1 5"/>
                            <path d="M4 9v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9"/>
                            <path d="M9 22V12h6v10"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['vendor_id'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg><?php echo htmlspecialchars($errors['vendor_id']); ?></div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Status Selection -->
                <!-- ========================= -->

                <div class="form-group <?php echo isset($errors['designation_id']) ? 'has-error' : ''; ?>">
                    <label for="status">Status <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select name="status" id="status">
                            <option value="">Select Status</option>
                            <?php $currentStat = $assetData['status'] ?? 'AVAILABLE'; ?>
                            <?php foreach ($statusEnum ?? [] as $status): ?>
                                <option value="<?php echo strtoupper($status) ?>" <?php echo($currentStat == strtoupper($status) ? 'selected' : ''); ?>><?php echo htmlspecialchars($status); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <line x1="4" y1="21" x2="4" y2="14"/>
                            <line x1="4" y1="10" x2="4" y2="3"/>
                            <line x1="12" y1="21" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12" y2="3"/>
                            <line x1="20" y1="21" x2="20" y2="16"/>
                            <line x1="20" y1="12" x2="20" y2="3"/>
                            <circle cx="4" cy="12" r="2"/>
                            <circle cx="12" cy="10" r="2"/>
                            <circle cx="20" cy="14" r="2"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['status'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg><?php echo htmlspecialchars($errors['status']); ?></div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Cost Field -->
                <!-- ========================= -->
                <div class="form-group <?php echo isset($errors['mobile']) ? 'has-error' : ''; ?>">
                    <label for="mobile">Cost <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="cost" id="cost" placeholder="ex. 48000" maxlength="10"
                               value="<?php echo htmlspecialchars($old['cost'] ?? $assetData['cost'] ?? ''); ?>">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M20.59 13.41L11 3H4v7l9.59 9.59a2 2 0 0 0 2.82 0l4.18-4.18a2 2 0 0 0 0-2.82z"/>
                            <circle cx="7.5" cy="7.5" r="1.5"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['cost'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($errors['cost']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Purchase Date Field -->
                <!-- ========================= -->
                <div class="form-group <?php echo isset($errors['mobile']) ? 'has-error' : ''; ?>">
                    <label for="mobile">Purchase Date <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="date" id="purchase_date" name="purchase_date"
                               value="<?= htmlspecialchars($old['purchase_date'] ?? $assetData['purchase_date'] ?? '') ?>">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['purchase_date'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($errors['purchase_date']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Warranty Date Field -->
                <!-- ========================= -->
                <div class="form-group <?php echo isset($errors['mobile']) ? 'has-error' : ''; ?>">
                    <label for="mobile">Warranty Date <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="date" id="warranty_date" name="warranty_date"
                               value="<?= htmlspecialchars($old['warranty_date'] ?? $assetData['warranty_date'] ?? '') ?>">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['warranty_date'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($errors['warranty_date']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Profile Image Upload -->
                <!-- ========================= -->
                <div class="form-group full-width <?php echo isset($errors['profile_image']) ? 'has-error' : ''; ?>">
                    <label for="profile_image">Profile Image (optional)</label>
                    <div class="input-wrapper">
                        <input type="file" name="profile_image" id="profile_image" accept=".png,.jpg,.jpeg,.webp">
                    </div>
                    <?php if (!empty($assetData['profile_image'])): ?>
                        <div class="error-text" style="margin-top: 10px; display: block; color: #475569;">
                            Current image: <strong><?php echo htmlspecialchars($assetData['profile_image']); ?></strong>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($errors['profile_image'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($errors['profile_image']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Form Actions -->
                <!-- Cancel & Save Buttons -->
                <!-- ========================= -->
                <div class="actions-row">
                    <a href="index.php?route=assets/show" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">
                        <span class="btn-content">
                            <svg viewBox="0 0 24 24"><path
                                        d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline
                                        points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Save Changes
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>