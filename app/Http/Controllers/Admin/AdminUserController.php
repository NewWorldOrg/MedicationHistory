<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as AppController;
use App\Services\Interfaces\AdminUserServiceInterface;
use App\Http\Requests\Admin\AdminUsers\CreateAdminUserRequest;
use App\Http\Requests\Admin\AdminUsers\UpdateAdminUserRequest;
use Domain\AdminUsers\AdminId;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Infra\EloquentModels\AdminUser;

class AdminUserController extends AppController
{

    protected AdminUserServiceInterface $adminUserService;

    public function __construct(AdminUserServiceInterface $adminUserService)
    {
        parent::__construct();
        $this->adminUserService = $adminUserService;
    }

    /**
     * Index of users.
     *
     * @return View
     */
    public function index(): View
    {
        $adminUsers = $this->adminUserService->getUsers();
        return view('admin_users.index', compact('adminUsers'));
    }

    /**
     * Form to create user.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin_users.create');
    }

    /**
     * Store a user.
     *
     * @param CreateAdminUserRequest $request
     * @return RedirectResponse
     */
    public function store(CreateAdminUserRequest $request): RedirectResponse
    {
        $this->adminUserService->createUser($request);
        return redirect(route('admin.admin_users.index'))->with('success', 'ユーザーを保存しました');
    }

    /**
     * Form to update the user.
     *
     * @param AdminUser $adminUser
     * @return View
     */
    public function edit(AdminUser $adminUser): View
    {
        return view('admin_users.edit', compact('adminUser'));
    }

    /**
     * Update the user.
     *
     * @param AdminUser $adminUser
     * @param UpdateAdminUserRequest $request
     * @return RedirectResponse
     */
    public function update(AdminUser $adminUser, UpdateAdminUserRequest $request): RedirectResponse
    {
        $this->adminUserService->updateUser(new AdminId((int)$adminUser->id), $request);
        return redirect(route('admin.admin_users.index'))->with(['success' => 'ユーザーを編集しました']);
    }

    /**
     * Delete the user.
     *
     * @param AdminUser $adminUser
     * @return RedirectResponse
     */
    public function destroy(AdminUser $adminUser): RedirectResponse
    {
        $this->adminUserService->deleteUser(new AdminId((int)$adminUser->id));
        return redirect(route('admin.admin_users.index'))->with(['success' => 'ユーザーを削除しました']);
    }
}
