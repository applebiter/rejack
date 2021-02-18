<?php 
namespace App\Form;

use Cake\Core\Configure;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class StopForm extends Form
{
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema->addField('stop', 'string');
    }
    
    public function validationDefault(Validator $validator): Validator
    {
        $validator->scalar('stop');
        
        return $validator;
    }
    
    protected function _execute(array $data): bool
    {
        return $this->stop();
    }
    
    protected function stop()
    {
        $cmd = sprintf(Configure::read('Rejack.Commands.stop'), $this->getPid());
        $r = shell_exec($cmd);
        $r = $this->getPid();
        $r = $r ? trim($r) : $r;
        
        return $r ? false : true;
    }
    
    protected function getPid()
    {
        $r = shell_exec(Configure::read('Rejack.Commands.getPid'));
        
        if ('false' !== stripos($r, ' ')) 
        {
            $arr = explode(' ', $r);
            $r = array_shift($arr);
        }
        
        return ($r) ? intval(trim($r)) : null;
    }
}