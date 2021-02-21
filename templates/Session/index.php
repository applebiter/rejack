<?php 
use Cake\Core\Configure;

function getPortForClient($client)
{
    $dir = Configure::read('Rejack.ClientsDir');
    $client = addcslashes($client, '$');
    $markfile = $dir . DS . $client;
    $contents = file_get_contents($markfile);
    $parts = explode(':', $contents);
    
    return intval($parts[1]);
}
?>

<?php if (!$isRunning) : ?>
  
<div class="jumbotron">
    
  <h1 class="display-3">
    Rejack is <span class="text-muted">Offline</span>
  </h1>
    
  <hr class="my-4">
    
  <?php echo $this->Form->create($startForm, [ 
      'url' => '/session/start' ,
      'class' => 'form-inline'
  ]) ?>
      
    <label class="my-1 mr-2" for="sampleRate">Sample Rate</label>
    <select class="custom-select my-1 mr-sm-2" id="sampleRate" name="sampleRate">
      <option value="22050">22050</option>
      <option value="32000">32000</option>
      <option value="44100" selected="selected">44100</option>
      <option value="48000">48000</option>
      <option value="88200">88200</option>
      <option value="96000">96000</option>
      <option value="192000">192000</option>
    </select>
      
    <label class="my-1 mr-2" for="bufferSize">Buffer Size</label>
    <select class="custom-select my-1 mr-sm-2" id="bufferSize" name="bufferSize">
      <option value="16">16</option>
      <option value="32">32</option>
      <option value="64">64</option>
      <option value="128">128</option>
      <option value="256" selected="selected">256</option>
      <option value="512">512</option>
      <option value="1024">1024</option>
      <option value="2048">2048</option>
      <option value="4096">4096</option>
    </select>
      
    <button type="submit" class="btn btn-primary my-1">Start Rejack</button>
      
  <?php echo $this->Form->end() ?>
    
</div>
  
<?php else : ?> 
  
  <div class="jumbotron">
    
    <span class="float-right">
      <a class="btn btn-secondary btn-sm" href="/session/connect">Patchbay</a> &nbsp; 
      <a class="btn btn-secondary btn-sm" href="/session/stream">Stream</a>
    </span>
  
    <h1 class="display-3">Rejack is <span class="text-primary">Online</span></h1>
    
    <p>
      Sample rate: 
      <strong><?= h($sampleRate) ?>Hz</strong> &nbsp; Buffer size: 
      <strong><?= h($bufferSize) ?></strong><br />
      Number of available user slots:  
      <strong><?php echo count($availablePorts) ?></strong>
    </p>
    
    <hr class="my-4">
    
    <?php if (count($availablePorts)) : ?> 
    
    <?php echo $this->Form->create($joinForm, [ 
        'url' => '/session/join' ,
        'class' => 'form-inline'
    ]) ?>
    
    <label class="my-1 mr-2" for="clientname">Client Name</label>
    <input type="text" class="form-control my-1 mr-sm-2" id="clientname" name="clientname" placeholder="ex: johnboy">
    
    <label class="my-1 mr-2" for="numchannels">Number of Channels</label>
      <select class="form-control my-1 mr-sm-2" id="numchannels" name="numchannels">
        <option value="1">1</option>
        <option value="2" selected="selected">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
      </select>
      
      <button type="submit" class="btn btn-secondary my-1">Join the Session</button>
    
    <?php echo $this->Form->end() ?>

    <hr class="my-4">
    
    <?php endif ?>
    
    <?php if (count($clients)) : ?>
    
    <p class="lead">
      <?= count($clients) ?> <?= count($clients) > 1 ? 'users have' : 'user has' ?> 
      joined the session.
    </p>
    
    <?php else : ?>
    
    <p class="lead">No users are in the session, yet.</p>
    
    <?php endif ?>
    
    <?php if (count($clients)) : ?>
    
    <div class="row">
    
    <?php foreach ($clients as $client => $parts) : ?>
    
    <div class="col-lg-3">
      <div class="card text-primary border-secondary mb-1">
        <div class="card-body">
          <div class="card-text">          
            <span class="badge badge-pill badge-info float-right">
              <?php if (isset($parts['out'])) : ?>
              <?= count($parts['out']) ?> <?= count($parts['out']) > 1 ? 'channels' : 'channel' ?>
              <?php endif ?>
            </span> <br /> 
            <span class="text-secondary"><strong><?= h($client) ?></strong></span> <br/>
            <small><a href="#" class="text-info" id="<?= h($client) ?>_opener">Connection Instructions</a></small>  
            <div class="modal-dialog" role="document" id="<?= h($client) ?>_instructions">
              <p>
                Copy and paste the following command into your terminal 
                and enter it to join the session: <br /><br />
                <code>jacktrip -c <?= $_SERVER['SERVER_ADDR']?> -n <?= count($parts['out']) ?> --peerport <?= getPortForClient($client) ?></code>
              </p>
            </div>	
          </div>
          <?php echo $this->Form->create($leaveForm, [ 
              'url' => '/session/leave' ,
              'class' => 'form-inline'
          ]) ?>
            <input type="hidden" id="clientname" name="clientname" value="<?= h($client) ?>">
            <button type="submit" 
                    class="btn btn-sm btn-secondary my-1"
                    onclick="return confirm('Are you sure you want to remove <?= h($client) ?> from the session?');">
                    Remove</button>          
          <?php echo $this->Form->end() ?>
        </div>
      </div>
    </div>
      
    <?php endforeach ?>
    
    </div>
    
    <?php endif ?>
    
    <br />
    
    <?php echo $this->Form->create($stopForm, [ 
        'url' => '/session/stop' ,
        'class' => 'form-inline'
    ]) ?>
      
      <button type="submit" 
              name="stop" 
              class="btn btn-secondary my-1"
              onclick="return confirm('Are you sure you want to stop the JACK server?');">Stop Rejack</button>
      
    <?php echo $this->Form->end() ?>
    
  </div>
  
  <?php endif ?>

<?php 
if (count($clients)) 
{
    $this->Html->scriptStart(['block' => true]);
    
    echo "$( document ).ready(function() { \n";
    
    foreach (array_keys($clients) as $client) 
    {
        echo "   $( \"#" . h($client) . "_instructions\" ).dialog({ \n" .
             "       autoOpen: false, \n" .
             "       show: { \n" .
             "           effect: \"fade\", \n" .
             "           duration: 500 \n" .
             "       }, \n" .
             "       hide: { \n" .
             "           effect: \"fade\", \n" .
             "           duration: 500 \n" .
             "       } \n" .
             "   }); \n" .
             "\n" .
             "   $( \"#" . h($client) . "_opener\" ).on( \"click\", function() { \n" .
             "       $( \"#" . h($client) . "_instructions\" ).dialog( \"open\" ); \n" .
             "   }); \n";
    }
        
    echo  "});";
    
    $this->Html->scriptEnd();
}
?> 
