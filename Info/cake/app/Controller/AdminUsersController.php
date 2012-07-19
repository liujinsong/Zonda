<?php
App::uses('AppController', 'Controller');
/**
 * AdminUsers Controller
 *
 * @property AdminUser $AdminUser
 */
class AdminUsersController extends AppController {

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->AdminUser->recursive = 0;
		$this->set('adminUsers', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->AdminUser->id = $id;
		if (!$this->AdminUser->exists()) {
			throw new NotFoundException(__('Invalid admin user'));
		}
		$this->set('adminUser', $this->AdminUser->read(null, $id));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->AdminUser->create();
			if ($this->AdminUser->save($this->request->data)) {
				$this->Session->setFlash(__('The admin user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The admin user could not be saved. Please, try again.'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->AdminUser->id = $id;
		if (!$this->AdminUser->exists()) {
			throw new NotFoundException(__('Invalid admin user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->AdminUser->save($this->request->data)) {
				$this->Session->setFlash(__('The admin user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The admin user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->AdminUser->read(null, $id);
		}
	}

/**
 * admin_delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->AdminUser->id = $id;
		if (!$this->AdminUser->exists()) {
			throw new NotFoundException(__('Invalid admin user'));
		}
		if ($this->AdminUser->delete()) {
			$this->Session->setFlash(__('Admin user deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Admin user was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
