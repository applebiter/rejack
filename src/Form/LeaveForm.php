<?php 
namespace App\Form;

use Cake\Core\Configure;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class LeaveForm extends Form
{
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema->addField('clientname', 'string');
    }
    
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->maxLength('clientname', 60)
            ->alphaNumeric('clientname');
        
        return $validator;
    }
    
    protected function _execute(array $data): bool
    {
        return $this->leave($data['clientname']);
    }
    
    protected function leave($clientname)
    {
        $markfile = Configure::read('Rejack.ClientsDir') . DS . addcslashes($clientname, '$');
        
        if (!$contents = file_get_contents($markfile))
        {
            return false;
        }
        
        $parts = explode(':', $contents);
        $pid = trim($parts[0]);
        $port = trim($parts[1]);        
        $cmd = sprintf(Configure::read('Rejack.Commands.stop'), $pid);
        
        shell_exec($cmd);        
        usleep(500000);
        
        if (!$this->releasePort($port))
        {
            return false;
        }
        
        return unlink($markfile);
    }
    
    protected function releasePort($port)
    {
        $markfile = Configure::read('Rejack.PortsDir') . DS . "$port";
        
        if (file_exists($markfile)) 
        {
            unlink($markfile);
        }
        
        return true;
    }
}