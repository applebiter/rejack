<?php 
namespace App\Form;

use Cake\Core\Configure;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class StreamForm extends Form
{
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema
            ->addField('inputs', ['type' => 'array'])
            ->addField('sample_rate', ['type' => 'integer'])
            ->addField('num_channels', ['type' => 'integer'])
            ->addField('stream_name', ['type' => 'string'])
            ->addField('stream_description', ['type' => 'string'])
            ->addField('icecast_mount', ['type' => 'string']);
    }
    
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->isArray('inputs')
            ->scalar('sample_rate')
            //->inList('sample_rate', [22050, 32000, 44100, 48000, 88200, 96000, 192000])
            ->scalar('num_channels')
            ->inList('num_channels', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])
            ->notBlank('stream_name')
            ->maxLength('stream_name', 150)
            ->allowEmptyString('stream_description')
            ->maxLength('stream_name', 250)
            ->notBlank('icecast_mount') 
            ->maxLength('icecast_mount', 40) 
            ->alphaNumeric('icecast_mount');
        
        return $validator;
    }
    
    protected function _execute($data): bool
    {
        return $this->send($data);
    }
    
    protected function send(array $data)
    {
        $inputs = $data['inputs'];
        
        foreach ($data['inputs'] as $idx => $input) 
        {
            $inputs[$idx] = $input;
        }
        
        $inputs = implode(' ', $inputs);
        $rate = intval($data['sample_rate']);
        $channels = intval($data['num_channels']);
        $quality = Configure::read('Rejack.Default.StreamConfig.OggencQuality');
        $name = escapeshellcmd($data['stream_name']);
        $description = escapeshellcmd($data['stream_description']);
        $host = Configure::read('Rejack.Default.StreamConfig.IcecastHost');
        $port = Configure::read('Rejack.Default.StreamConfig.IcecastPort');
        $passwd = Configure::read('Rejack.Default.StreamConfig.SourcePasswd');
        $mount = $data['icecast_mount'];
        $dir = Configure::read('Rejack.StreamsDir');
        $cmd = sprintf(Configure::read('Rejack.Commands.stream'), 
            $inputs, $rate, $channels, $quality, $name, $description, $host, 
            $port, $passwd, $mount, $dir, $mount);
        
        shell_exec("($cmd) &");
        
        return true;
    }
}