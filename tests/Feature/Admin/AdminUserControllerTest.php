<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use Courage\CoString;
use Domain\AdminUser\AdminUserId;
use Domain\Exception\NotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infra\EloquentModels\AdminUser as AdminUserModel;
use Infra\EloquentRepository\AdminUserRepository;
use Tests\TestCase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase;
    private AdminUserRepository $adminUserRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->adminLogin();
        $this->adminUserRepository = $this->app->make(AdminUserRepository::class);
    }

    public function testIndex()
    {
        $this->get(route('admin.admin_users.index'))->assertOk();
    }

    public function testCreate()
    {
        $this->get(route('admin.admin_users.create'))->assertOk();
    }

    public function testStore()
    {
        $params = [
            'user_id' => 'test_takada_yuki',
            'password' => 'hogehoge',
            'password_confirm' => 'hogehoge',
            'name' => '高田憂希',
            'role' => 1,
            'status' => 1,
        ];

        $this->post(route('admin.admin_users.store'), $params)
            ->assertRedirect(route('admin.admin_users.index'))
            ->assertSessionHas('success');

        $response = $this->adminUserRepository->getByUserId(new AdminUserId($params['user_id']));

        $this->assertTrue(
            $response->getUserId()->isEqual(new Costring($params['user_id']))
        );
    }

    public function testEdit()
    {
        $this->get(route('admin.admin_users.edit', $this->drug->getId()))->assertOk();
    }

    public function testUpdate()
    {
        $params = [
            'user_id' => 'test_takada_yuki',
            'password' => 'hogehoge',
            'name' => '高田憂希',
            'role' => 1,
            'status' => 1,
        ];

        AdminUserModel::create($params);

        $response = $this->adminUserRepository->getByUserId(new AdminUserId($params['user_id']));

        $params['user_id'] = 'takada_yuki_test';
        $params['password_confirm'] = 'hogehoge';

        $this->put(route('admin.admin_users.update', $response->getId()), $params)
            ->assertRedirect(route('admin.admin_users.index'))
            ->assertSessionHas('success');

        $response = $this->adminUserRepository->getByUserId(new AdminUserId($params['user_id']));

        $this->assertTrue(
            $response->getUserId()->isEqual(new CoString($params['user_id']))
        );
    }

    public function testDestroy()
    {
        $params = [
            'user_id' => 'test_takada_yuki',
            'password' => 'hogehoge',
            'name' => '高田憂希',
            'role' => 1,
            'status' => 1,
        ];

        AdminUserModel::create($params);

        $response = $this->adminUserRepository->getByUserId(new AdminUserId($params['user_id']));

        $this->delete(route('admin.admin_users.destroy', $response->getId()))
            ->assertRedirect(route('admin.admin_users.index'))
            ->assertSessionHas('success');

        $this->expectException(NotFoundException::class);
        $this->adminUserRepository->getByUserId(new AdminUserId($params['user_id']));
    }
}
