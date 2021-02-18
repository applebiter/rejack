<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\StartForm;
use App\Form\StopForm;
use App\Form\JoinForm;
use App\Form\LeaveForm;
use App\Form\ConnectForm;
use App\Form\DisconnectForm;
use App\Form\SnapshotForm;
use App\Form\RestoreForm;
use Cake\Core\Configure;
use Cake\Utility\Xml;
use Exception;

/**
 * Session Controller
 *
 * @property 
 * @method \App\Model\Entity\Sound[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SessionController extends AppController
{ 
    public $isRunning = false;
    public $pid = null;
    public $sampleRate = null;
    public $bufferSize = null;
    public $clients = [];
    public $availablePorts = [];
    public $currentSnapshot = [];
    
    public function initialize(): void
    {
        parent::initialize();
        
        if ($this->isRunning = $this->isRunning()) 
        {
            $this->pid = $this->getPid();
            $this->availablePorts = $this->availablePorts();
            
            $this->parseSettings();
            $this->loadClients();
            $this->loadCurrentSnapshot();
        }
        else 
        {
            $this->clearClients();
            $this->clearPorts();
        }
        
        $this->set('isRunning', $this->isRunning);
        $this->set('pid', $this->pid);
        $this->set('sampleRate', $this->sampleRate);
        $this->set('bufferSize', $this->bufferSize);
        $this->set('clients', $this->clients);
        $this->set('availablePorts', $this->availablePorts);
        $this->set('currentSnapshot', $this->currentSnapshot);
    }
    
    /**
     * Index method 
     * 
     */
    public function index() 
    {
        $startForm = new StartForm();
        $stopForm = new StopForm();
        $joinForm = new JoinForm();
        $leaveForm = new LeaveForm();
        
        $this->set('startForm', $startForm);
        $this->set('stopForm', $stopForm);
        $this->set('joinForm', $joinForm);
        $this->set('leaveForm', $leaveForm);
    }
    
    /**
     * Start method 
     * 
     * @return \Cake\Http\Response
     */
    public function start() 
    {
        if ($this->request->is(['patch', 'post', 'put']))
        { 
            $form = new StartForm();
            
            $form->execute($this->request->getData());
        }
        
        return $this->redirect($this->referer());
    }
    
    /**
     * Stop method 
     * 
     * @return \Cake\Http\Response
     */
    public function stop() 
    {
        if ($this->request->is(['patch', 'post', 'put']))
        {
            $form = new StopForm();
            
            $form->execute($this->request->getData());
        }
        
        return $this->redirect($this->referer());
    }
    
    /**
     * Join method 
     * 
     * @return \Cake\Http\Response
     */
    public function join() 
    {
        if ($this->request->is(['patch', 'post', 'put']))
        {
            $form = new JoinForm();
            
            $form->execute($this->request->getData());
        }
        
        return $this->redirect($this->referer());
    }
    
    /**
     * Leave method 
     * 
     * @return \Cake\Http\Response
     */
    public function leave() 
    {
        if ($this->request->is(['patch', 'post', 'put']))
        {
            $form = new LeaveForm();
            
            $form->execute($this->request->getData());
        }
        
        return $this->redirect($this->referer());
    }
    
    /**
     * Connect method 
     * 
     * @return \Cake\Http\Response
     */
    public function connect() 
    {
        $form = new ConnectForm();
        
        if ($this->request->is(['patch', 'post', 'put']))
        {
            $form->execute($this->request->getData());
            
            return $this->redirect($this->referer());
        }
        
        $this->set('form', $form);
    }
    
    /**
     * Disconnect method 
     * 
     * @return \Cake\Http\Response
     */
    public function disconnect() 
    {
        if ($this->request->is(['patch', 'post', 'put']))
        {
            $form = new DisconnectForm();
            
            $form->execute($this->request->getData());
        }
        
        return $this->redirect($this->referer());
    }
    
    /**
     * Theme method 
     * 
     * @param string $theme
     * @return \Cake\Http\Response
     */
    public function theme($theme = null)
    {
        $markfile = Configure::read('Rejack.ThemeMarker');
        $themes = Configure::read('Rejack.Themes');
        
        if ($theme && in_array($theme, $themes))
        {
            file_put_contents($markfile, $theme, LOCK_EX);
        }
        
        return $this->redirect($this->referer());
    }
    
    /**
     * IsRunning method 
     * 
     * @return boolean
     */
    protected function isRunning() 
    {
        $r = shell_exec(Configure::read('Rejack.Commands.isRunning'));
        $r = $r ? trim($r) : $r;
        
        return 'running' == $r ? true : false;
    }
    
    /**
     * ParseSettings method 
     * 
     */
    protected function parseSettings() 
    {
        $r = shell_exec(Configure::read('Rejack.Commands.bufsize'));
        
        if ($r) 
        {
            $ra = explode(' ', $r);
            $this->sampleRate = $ra[8];
            $this->bufferSize = $ra[3];
        }
    }
    
    /**
     * GetPid method 
     * 
     * @return NULL|number
     */
    protected function getPid() 
    {
        $r = shell_exec(Configure::read('Rejack.Commands.getPid'));
        $r = $r ? trim($r) : $r;
        
        return $r ? intval($r) : null;
    }
    
    /**
     * AvailablePorts method 
     * 
     * @return array
     */
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
    
    /**
     * LoadClients method 
     * 
     */
    protected function loadClients() 
    {
        $raw = shell_exec(Configure::read('Rejack.Commands.loadClients'));
        $raw = $raw ? trim($raw) : $raw;
        
        if ($raw) 
        {
            $ports = explode("\n", $raw);
            
            foreach ($ports as $port) 
            {
                $parts = explode(':', $port);
                $client = $parts[0];
                $channel = $parts[1];
                
                if (strripos(trim($channel), 'capture') !== false 
                    || strripos(trim($channel), 'receive') !== false 
                    || strripos(trim($channel), 'in') !== false)
                { 
                    $this->clients[addcslashes(trim($client), '$')]['in'][] = trim($channel);
                }
                else 
                { 
                    $this->clients[addcslashes(trim($client), '$')]['out'][] = trim($channel);
                }
            }
        }
    }
    
    /**
     * ClearClients method 
     * 
     */
    protected function clearClients()
    {
        $dir = Configure::read('Rejack.ClientsDir');
        $raw = scandir($dir);
        
        foreach ($raw as $file) 
        {
            if ('.' != $file && '..' != $file) 
            {
                $path = $dir . DS . $file;
                
                unlink($path);
            }
        }
    }
    
    /** 
     * ClearPorts method 
     * 
     */
    protected function clearPorts()
    {
        $dir = Configure::read('Rejack.PortsDir');
        $raw = scandir($dir);
        
        foreach ($raw as $file)
        {
            if ('.' != $file && '..' != $file)
            {
                $path = $dir . DS . $file;
                
                unlink($path);
            }
        }
    }
    
    /**
     * LoadCurrentSnapshot method 
     * 
     */
    protected function loadCurrentSnapshot() 
    {
        $dir = Configure::read('Rejack.SnapshotsDir');
        $snap = $dir . DS . 'current.snap';        
        $cmd = sprintf(Configure::read('Rejack.Commands.snapshot'), $dir, 'current');
        
        shell_exec($cmd);
        usleep(250000);        
        
        $xml = file_get_contents($snap);
        $snapshot = Xml::build($xml);
        
        foreach ($snapshot->jack->client as $client) 
        {
            $clientname = (string) $client['name'];
            
            foreach ($client->port as $receiver)
            {
                $receivername = (string) $receiver['name'];
                
                foreach ($receiver->connection as $sender)
                { 
                    $source = (string) $sender['port'];
                    $sourceArr = explode(':', $source);
                    $sourceClient = $sourceArr[0];
                    $sourceChannel = $sourceArr[1];
                    
                    $this->currentSnapshot[$clientname][$receivername][$sourceClient][] = $sourceChannel;
                }
            }
        }
    }
}