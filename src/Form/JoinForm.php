<?php 
namespace App\Form;

use Cake\Core\Configure;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class JoinForm extends Form
{
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema
            ->addField('clientname', 'string')
            ->addField('numchannels', 'integer');
    }
    
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->maxLength('clientname', 60)
            ->alphaNumeric('clientname')
            ->scalar('numchannels')
            ->inList('numchannels', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        
        return $validator;
    }
    
    protected function _execute(array $data): bool
    {
        return $this->join($data['numchannels'], $data['clientname']);
    }
    
    protected function join($numchannels, $clientname)
    {
        $availablePorts = $this->availablePorts();
        
        if (!count($availablePorts))
        {
            return false;
        }
        
        $port = $availablePorts[0];
        
        if (!$this->occupyPort($port))
        {
            return false;
        }
        
        $cmd = sprintf(Configure::read('Rejack.Commands.join'),
                       intval($port),
                       intval($numchannels),
                       addcslashes($clientname, '$'),
                       Configure::read('Rejack.LogsDir'),
                       addcslashes($clientname, '$'));
        
        $pid = shell_exec($cmd);
        
        usleep(500000);
        
        $pid = $pid ? trim($pid) : $pid;
        $markfile = Configure::read('Rejack.ClientsDir') . DS . addcslashes($clientname, '$');
        
        if (!file_put_contents($markfile, "$pid:$port"))
        {
            return false;
        }
        
        return true;
    }
    
    protected function availablePorts()
    {
        $allPorts = Configure::read('Rejack.Ports');
        $portsDir = Configure::read('Rejack.PortsDir');
        $raw = scandir($portsDir);
        $usedPorts = [];
        $availablePorts = [];
        
        foreach ($raw as $file)
        {
            if ('.' != $file && '..' != $file)
            {
                $usedPorts[] = intval(trim(file_get_contents(Configure::read('Rejack.PortsDir') . DS . $file)));
            }
        }
        
        $diff = array_diff($allPorts, $usedPorts);
        
        foreach ($diff as $port)
        {
            if ($port)
            {
                $availablePorts[] = $port;
            }
        }
        
        return $availablePorts;
    }
    
    protected function occupyPort($port)
    {
        $availablePorts = $this->availablePorts();
        
        if (!in_array($port, $availablePorts)) 
        {
            return false;
        }
        
        $portsDir = Configure::read('Rejack.PortsDir');
        $markfile = $portsDir . DS . "$port";
        
        return file_put_contents($markfile, $port) ? true : false;
    } 
}