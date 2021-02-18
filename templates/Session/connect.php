<div class="jumbotron">

  <span class="float-right"><a class="btn btn-secondary btn-sm" href="/">Return to the Dashboard</a></span>

  <h1 class="display-3">
    Rejack <span class="text-primary">Patchbay</span>
  </h1>
    
  <hr class="my-4">
  
  <div class="row"> 
  
    <div class="col-lg-6"> 
    
      <?php echo $this->Form->create($form, [ 
          'url' => '/session/connect' 
      ]) ?>
      
      <?php if (count($clients) > 1) : ?>
      <?php $clones = $clients ?>
      <?php foreach ($clients as $client => $io) : ?>
      
        <fieldset>
          
          <legend class="text-primary">Client &#34;<?= h($client) ?>&#34;</legend>
          
          <div class="row">
            <?php foreach ($io as $direction => $channels) : ?>
            <?php foreach ($channels as $idx => $endpoint) :?>
            <?php if ('in' == $direction) : ?>
            <div class="form-group col-lg-6">
              <span class="text-info"><strong>Receiver Channel <?= $idx + 1 ?></strong></span>
              <label for="receivers">
                <small>Select one or more source channels to connect with this receiving channel:</small>
              </label>
              <?php foreach ($clones as $clone => $oi) : ?>
              <?php if ($client != $clone) : ?>
              <select multiple="multiple" 
                      class="form-control" 
                      size="<?= count($oi['in']) ?>" 
                      id="receivers" 
                      name="receivers[<?= h($client) ?>][<?= h($endpoint) ?>][<?= h($clone) ?>]">                
                <?php foreach ($oi as $gender => $endpoints) : ?>
                <?php foreach ($endpoints as $key => $channel) : ?>
                <?php if ('out' == $gender) : ?>
                <?php 
                $hasSource = isset($currentSnapshot[$client][$endpoint][$clone]);
                $selected = ($hasSource && in_array($channel, $currentSnapshot[$client][$endpoint][$clone])) 
                    ? ' selected=\"selected\"' 
                    : '';
                ?>
                <option value="<?= h($channel) ?>"<?= h($selected) ?>><?= h($clone) ?>: channel <?= $key + 1?></option>
                <?php endif ?>
                <?php endforeach ?>
                <?php endforeach ?>
              </select>
              <?php endif ?>
              <?php endforeach ?>
            </div>
            <?php endif ?>
            <?php endforeach ?>
            <?php endforeach ?>
          </div>
          
        </fieldset>
        
        <hr class="my-4">
        
        <?php endforeach ?>
        
        <button type="submit" class="btn btn-primary my-1">Apply Connections</button>
        
        <?php endif ?>
        
      <?php echo $this->Form->end() ?>
      
    </div>
  
    <div class="col-lg-6"> 
      <div class="card mb-3">
          <h3 class="card-header">Current Connections</h3>
          <?php if (count($currentSnapshot)) : ?>
          <ul class="list-group list-group-flush">
            <?php foreach ($currentSnapshot as $receiver => $inputs) : ?>
            <li class="list-group-item bg-light">
              <h6 class="text-primary"><?= h($receiver) ?></h6>
              <ul class="list-group list-group-flush">
                <?php foreach ($inputs as $receivername => $senders) : ?>
                <?php 
                    $receivernameArr = explode('_', $receivername);
                    $inChannel = array_pop($receivernameArr);
                ?>
                <li class="list-group-item">
                  <p class="text-info">Receiver Channel <?= h($inChannel)  ?>:</p>
                  <ul class="list-group list-group-flush">
                    <?php foreach ($senders as $sender => $outputs) : ?>
                    <?php foreach ($outputs as $output) : ?> 
                    <?php 
                        $outputArr = explode('_', $output);
                        $outChannel = array_pop($outputArr);
                    ?>
                    <li class="list-group-item">
                      <?= h($sender) ?> : channel <?= h($outChannel) ?> &nbsp; &nbsp; 
                      <?= $this->Form->postLink('<span class="badge badge-pill badge-secondary">Disconnect</span>', 
                      ['action' => 'disconnect'], 
                      [ 
                          'data' => [ 
                              'clienta' => h($receiver) . ':receive_' . h($inChannel),
                              'clientb' => h($sender) . ':send_' . h($outChannel)
                          ],
                          'escape' => false 
                      ]) ?> 
                    </li> 
                	<?php endforeach ?>
                	<?php endforeach ?>
                  </ul>
                </li>
                <?php endforeach ?>
              </ul>
            </li>
            <?php endforeach ?>
          </ul>
          <?php endif ?>
        </div>
    </div>  
  </div>    
</div>
