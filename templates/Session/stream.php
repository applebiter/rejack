<?php 
use Cake\Core\Configure;
?>
  
  <div class="jumbotron">
    
    <span class="float-right">
      <a class="btn btn-secondary btn-sm" href="/">Dashboard</a> &nbsp; 
      <a class="btn btn-secondary btn-sm" href="/session/connect">Patchbay</a>
    </span>
  
    <h1 class="display-3">Rejack <span class="text-primary">Streams</span></h1>
    
    <hr class="my-4">
    
    <?php echo $this->Form->create($form, [
        'url' => '/session/stream', 
        'id' => 'streamForm'
    ]) ?>
      <fieldset>
        <legend>Define a New Broadcast Stream</legend>
        
        <div class="row">
          <div class="col-lg-6">
            <div class="row">
              <div class="form-group col-lg-12">
                <label for="stream_name">Stream Name</label>
                <input type="text" class="form-control" id="stream_name" name="stream_name" aria-describedby="stream_name_help" placeholder="Live stream from johnboy @Home">
                <small id="stream_name_help" class="form-text text-muted">This is the stream name displayed in the stream player</small>
              </div>	
              
              <div class="form-group col-lg-12">
                <label for="stream_description">Stream Description</label>
                <textarea class="form-control" id="stream_description" name="stream_description" rows="3"></textarea>
              </div>
            
              <div class="form-group col-lg-6">
                <label for="num_channels">Number of Channels</label>
                <select class="form-control" id="num_channels" name="num_channels">
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
              </div>
            
              <div class="form-group col-lg-6">
                <label for="icecast_mount">Icecast Mountpoint</label>
                <input type="text" class="form-control" id="icecast_mount" name="icecast_mount" aria-describedby="icecast_mount_help" placeholder="johnboy">
                <small id="icecast_mount_help" class="form-text text-muted">Omit the forward slash and the file extension from /johnboy.ogg</small>
              </div>
            </div>
            
            <div class="form-group">
                <label for="inputs">Select Input Channels</label>
                <select multiple="multiple" 
                        size="<?= 2 * count($clients)?>" 
                        class="form-control" 
                        id="inputs" 
                        name="inputs[]" 
                        aria-describedby="inputs_help" >
                  <?php foreach (array_keys($clients) as $client) : ?>
                  <?php foreach ($clients[$client]['in'] as $channel) : ?>
                  <option value="<?= h($client) ?>:<?= h($channel) ?>"><?= h($client) ?>:<?= h($channel) ?></option>
                  <?php endforeach ?>
                  <?php endforeach ?>
                </select>
                <small id="inputs_help" class="form-text text-danger">
                  Do not connect more inputs than the number of channels you specified above or playback will be unstable and just bad.
                </small>
              </div>
              <button type="submit" 
                      class="btn btn-primary my-1 float-right" 
                      onclick="return confirm('After clicking \'OK\', you will need to manually navigate away from this page.')">Start Streaming</button>
          </div>
          <div class="col-lg-6">
            
            <?php if (isset($stats->icestats->source)) : ?>   
            <?php 
            $host = Configure::read('Rejack.Default.StreamConfig.IcecastHost');
            $port = Configure::read('Rejack.Default.StreamConfig.IcecastPort');
            $proto = Configure::read('Rejack.Default.StreamConfig.SSL') ? 'https' : 'http';
            ?>         
            <?php if (is_array($stats->icestats->source)) : ?>            
            <?php foreach ($stats->icestats->source as $stream) : ?>
            <?php 
            $url = (string) $stream->listenurl;
            $name = (string) $stream->server_name;
            $description = (string) $stream->server_description;
            $channels = isset($stream->audio_channels) ? (string) $stream->audio_channels : null;
            $sampleRate = (string) $stream->audio_samplerate;
            $bitrate =  (string) $stream->audio_bitrate;
            $tmp = explode('/', $url);
            $tmp = array_pop($tmp);
            $tmp = explode('.', $tmp);
            $mount = array_shift($tmp);
            ?>
            <div class="card">
              <h3 class="card-header">Now Streaming</h3>
              <div class="card-body">
                <h4 class="card-title"><?= h($name) ?></h4>
                <h6 class="card-subtitle mb-2 text-muted">Icecast Stream: <a href="<?= h($url) ?>" target="_blank" class="card-link"><?= h($url) ?></a></h6>
                <p class="card-text">                      
                  <?= ('Unspecified description' == $description) ?  '' : h($description) ?>                     
                </p>
                <a href="<?= h($proto) ?>://<?= h($host) ?>:<?= h($port) ?>/admin/killsource.xsl?mount=/<?= h($mount) ?>.ogg" target="_blank" class="card-link">Stop Streaming</a> 
              </div>
            </div>            
            <?php endforeach ?>  
            <?php else : ?>
            <?php 
            $stream = $stats->icestats->source;
            $url = (string) $stream->listenurl;
            $name = (string) $stream->server_name;
            $description = (string) $stream->server_description;
            $channels = isset($stream->audio_channels) ? (string) $stream->audio_channels : null;
            $sampleRate = (string) $stream->audio_samplerate;
            $bitrate =  (string) $stream->audio_bitrate;
            $tmp = explode('/', $url);
            $tmp = array_pop($tmp);
            $tmp = explode('.', $tmp);
            $mount = array_shift($tmp);
            ?>
            <div class="card">
              <h3 class="card-header">Now Streaming</h3>
              <div class="card-body">
                <h4 class="card-title"><?= h($name) ?></h4>
                <h6 class="card-subtitle mb-2 text-muted">Icecast Stream: <a href="<?= h($url) ?>" target="_blank" class="card-link"><?= h($url) ?></a></h6>
                <p class="card-text">                      
                  <?= ('Unspecified description' == $description) ?  '' : h($description) ?>                      
                </p>
                <a href="<?= h($proto) ?>://<?= h($host) ?>:<?= h($port) ?>/admin/killsource.xsl?mount=/<?= h($mount) ?>.ogg" target="_blank" class="card-link">Stop Streaming</a> 
              </div>
            </div>
            </div>            
            <?php endif ?>
            <?php endif ?>
          </div>          
        </div>
        
        <input type="hidden" id="sample_rate" name="sample_rate" value="<?= $sampleRate ?>">        
      </fieldset>
    
    <?php echo $this->Form->end() ?>
    
  </div>