<div class="jumbotron">

  <span class="float-right">
      <a class="btn btn-secondary btn-sm" href="/">Dashboard</a> &nbsp; 
      <a class="btn btn-secondary btn-sm" href="/session/stream">Stream</a>
    </span>

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
      <?php foreach ($clients as $client => $bus1) : ?>
      
        <fieldset>          
          <legend class="text-secondary">Source &#34;<?= h($client) ?>&#34;</legend>
          <div class="row">
            <?php foreach ($bus1['in'] as $source) : ?>
			<div class="form-group col-lg-6">
              <span class="text-info"><strong><?= h($source) ?></strong></span> &nbsp; 
              <label>
                <small>Select one or more listening channels to connect with this source channel:</small>
              </label>
              <?php foreach ($clones as $clone => $bus2) : ?>
              <?php if ($clone != $client) : ?>
			  <select multiple="multiple" 
                      class="form-control" 
                      size="<?= count($bus2['in']) ?>" 
                      name="receivers[<?= h($client) ?>][<?= h($source) ?>][<?= h($clone) ?>]">                
                <?php foreach ($bus2['out'] as $listener) : ?>
                <?php 
                $selected = '';                
                if (isset($currentSnapshot[$client][$source]) && is_array($currentSnapshot[$client][$source])) 
                {
                    $fullname = $clone . ':' . $listener;
                    
                    if (in_array($fullname, $currentSnapshot[$client][$source]))
                    {
                        $selected = ' selected="selected"';
                    }
                }
                ?>
				<option value="<?= h($listener) ?>"<?= $selected ?>><?= h($clone) ?>:<?= $listener ?></option>
                <?php endforeach ?>
              </select>
              <?php endif ?>
              <?php endforeach ?>
            </div>
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
            <?php foreach ($currentSnapshot as $client => $inputs) : ?>
            <li class="list-group-item bg-light">
              <h6 class="text-primary"><?= h($client) ?></h6>
              <ul class="list-group list-group-flush">
                <?php foreach ($inputs as $inputname => $listeners) : ?>
                <?php 
                $inputnameArr = explode('_', $inputname);
                $inName = $inputnameArr[0];
                $inChannel = $inputnameArr[1];
                ?>
                <li class="list-group-item">
                  <p class="text-info">
                    <?= h($client)  ?>:<?= h($inputname)  ?> has <?= count($listeners) ?> <?= count($listeners) > 1 ? 'listeners' : 'listener' ?>:
                  </p>
                  <ul class="list-group list-group-flush">
                    <?php foreach ($listeners as $idx => $listeningPort) : ?>
                    <?php 
                    $lpArr = explode(':', $listeningPort);
                    $listener = $lpArr[0];
                    $outChannel = $lpArr[1];
                    ?>
                    <li class="list-group-item">
                      <?= h($listener) ?> : <?= h($outChannel) ?> &nbsp; &nbsp; 
                      <?= $this->Form->postLink('<span class="badge badge-pill badge-secondary">Disconnect</span>', 
                      ['action' => 'disconnect'], 
                      [ 
                          'data' => [ 
                              'clienta' => h($client . ':' . $inputname),
                              'clientb' => h($listeningPort)
                          ],
                          'escape' => false 
                      ]) ?> 
                    </li> 
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
