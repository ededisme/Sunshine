<?php

class AppointmentsController extends AppController {

    var $name = 'Appointments';

    function index() {
        $this->layout = 'ajax';
    }

    function ajax($customer = 'all', $dateFrom = '', $dateTo = '') {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->set('userId', $user['User']['id']);
        $this->set(compact('customer', 'dateFrom', 'dateTo'));
    }

    function add() {
        $this->layout = 'ajax';
        $this->loadModel('User');
        if (!empty($this->data)) {
            $this->Appointment->create();
            $user = $this->getCurrentUser();
            $this->data['Appointment']['created_by'] = $user['User']['id'];
            if ($this->Appointment->save($this->data)) {
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        }
        $doctors = $this->User->find('all', array('conditions' => array('User.is_active' => 1,'UserGroup.group_id' => array('2', '21')), 'order'=>array('Employee.name ASC'),'group' => 'User.id',
                    'fields' => array('User.id, Employee.name, Company.id'),
                    'joins' => array(
                        array('table' => 'user_employees',
                            'alias' => 'UserEmployee',
                            'type' => 'INNER',
                            'conditions' => array(
                                'User.id = UserEmployee.user_id'
                            )
                        ),
                        array('table' => 'employees',
                            'alias' => 'Employee',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Employee.id = UserEmployee.employee_id'
                            )
                        ),                        
                        array('table' => 'user_companies',
                            'alias' => 'UserCompany',
                            'type' => 'INNER',
                            'conditions' => array(
                                'UserCompany.user_id = User.id'
                            )
                        ),
                        array('table' => 'companies',
                            'alias' => 'Company',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Company.id = UserCompany.company_id'
                            )
                        ),
                        array('table' => 'user_groups',
                            'alias' => 'UserGroup',
                            'type' => 'INNER',
                            'conditions' => array(
                                'User.id = UserGroup.user_id'
                            )
                        )
                )));
        $this->set(compact('doctors'));
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        $this->loadModel('User');
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $this->data['Appointment']['modified_by'] = $user['User']['id'];
            $this->data['Appointment']['modified'] = date("Y-m-d H:i:s");
            if ($this->Appointment->save($this->data)) {
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Appointment->read(null, $id);
            $doctors = $this->User->find('all', array('conditions' => array('User.is_active' => 1,'UserGroup.group_id' => array('2', '21')), 'order'=>array('Employee.name ASC'),'group' => 'User.id',
                    'fields' => array('User.id, Employee.name, Company.id'),
                    'joins' => array(
                        array('table' => 'user_employees',
                            'alias' => 'UserEmployee',
                            'type' => 'INNER',
                            'conditions' => array(
                                'User.id = UserEmployee.user_id'
                            )
                        ),
                        array('table' => 'employees',
                            'alias' => 'Employee',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Employee.id = UserEmployee.employee_id'
                            )
                        ),                        
                        array('table' => 'user_companies',
                            'alias' => 'UserCompany',
                            'type' => 'INNER',
                            'conditions' => array(
                                'UserCompany.user_id = User.id'
                            )
                        ),
                        array('table' => 'companies',
                            'alias' => 'Company',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Company.id = UserCompany.company_id'
                            )
                        ),
                        array('table' => 'user_groups',
                            'alias' => 'UserGroup',
                            'type' => 'INNER',
                            'conditions' => array(
                                'User.id = UserGroup.user_id'
                            )
                        )
                )));
            $this->set(compact('doctors'));
        }
    }
    
    function dashboardAppointmentAjax($date = '') {
        $this->layout = 'ajax';
        $this->set(compact('date'));
    }

    function cancelAppointment($id = null) {
        $user = $this->getCurrentUser();
        $this->loadModel('Appointment');
        $Appointment['Appointment']['id'] = $id;
        $Appointment['Appointment']['is_close'] = 1;
        $Appointment['Appointment']['modified_by'] = $user['User']['id'];
        $Appointment['Appointment']['modified'] = date("Y-m-d H:i:s");
        $this->Appointment->save($Appointment);
        echo MESSAGE_DATA_HAS_BEEN_SAVED;
        exit;
    }

}

?>