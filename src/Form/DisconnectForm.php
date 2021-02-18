<?php 
namespace App\Form;

use Cake\Core\Configure;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class DisconnectForm extends Form
{
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema
            ->addField('clienta', 'string')
            ->addField('clientb', 'string');
    }
    
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('clienta')
            ->maxLength('clienta', 70)
            ->scalar('clientb')
            ->maxLength('clientb', 70);
        
        return $validator;
    }
    
    protected function _execute(array $data): bool
    {
        return $this->disconnect($data['clienta'], $data['clientb']);
    }
    
    protected function disconnect($a, $b)
    {
        $a = addcslashes($a, '$');
        $b = addcslashes($b, '$');
        
        $cmd = sprintf(Configure::read('Rejack.Commands.disconnect'), $a, $b);
        
        shell_exec($cmd);
        usleep(125000);
        
        return true;
    }
}