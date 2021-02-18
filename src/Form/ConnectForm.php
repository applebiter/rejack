<?php 
namespace App\Form;

use Cake\Core\Configure;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class ConnectForm extends Form
{
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema->addField('receivers', ['type' => 'array']);
    }
    
    public function validationDefault(Validator $validator): Validator
    {
        $validator->isArray('receivers');
        
        return $validator;
    }
    
    protected function _execute(array $data): bool
    {
        return $this->connect($data['receivers']);
    }
    
    protected function connect(array  $receivers)
    {
        foreach ($receivers as $receiver => $inputs) 
        {
            foreach ($inputs as $input => $outputs) 
            {
                foreach ($outputs as $sender => $output) 
                {
                    $cmd = sprintf(Configure::read('Rejack.Commands.connect'),
                        h($receiver),
                        h($input),
                        h($sender),
                        h($output));
                    
                    shell_exec($cmd);
                    usleep(125000);
                }
            }
        }
        
        return true;
    }
}