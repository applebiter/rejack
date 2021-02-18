<?php 
namespace App\Form;

use Cake\Core\Configure;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class StartForm extends Form
{
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema
            ->addField('sampleRate', 'integer')
            ->addField('bufferSize', 'integer');
    }
    
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('sampleRate')
            ->inList('sampleRate', [22050, 32000, 44100, 48000, 88200, 96000, 192000])
            ->scalar('bufferSize')
            ->inList('bufferSize', [16, 32, 64, 128, 256, 512, 1024, 2048, 4096]);
        
        return $validator;
    }
    
    protected function _execute(array $data): bool
    {
        return $this->start($data['sampleRate'], $data['bufferSize']);
    }
    
    protected function start(int $sampleRate, int $bufferSize)
    {
        $cmd = sprintf(Configure::read('Rejack.Commands.start'),
                       $sampleRate, 
                       $bufferSize,
                       Configure::read('Rejack.LogsDir'));        
        $r = shell_exec($cmd);
        
        sleep(1);
        
        $r = $r ? trim($r) : $r;
        
        return $r ? true : false;
    }
}