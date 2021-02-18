<?php 

return [
    
    'Rejack' => [
        
        'DefaultTheme' => 'cerulean',
        'Themes' => [
            'cerulean', 'cosmo', 'cyborg', 'darkly', 'flatly', 'journal', 
            'litera', 'lumen', 'lux', 'materia', 'minty', 'pulse', 'sandstone', 
            'simplex', 'sketchy', 'slate', 'solar', 'spacelab', 'superhero', 
            'united', 'yeti'
        ],
        'ThemeMarker' => ROOT . DS . 'data' . DS . 'theme',
        'LogsDir' => ROOT . DS . 'data' . DS . 'logs',
        'Ports' => [4464, 4465, 4466, 4467, 4468, 4469, 4470, 4471, 4472, 4473],
        'PortsDir' => ROOT . DS . 'data' . DS . 'ports',
        'ClientsDir' => ROOT . DS . 'data' . DS . 'clients',
        'SnapshotsDir' => ROOT . DS . 'data' . DS . 'snapshots',
        'Commands' => [ 
            'isRunning' => 'jack_wait -c', 
            'bufsize' => 'jack_bufsize',
            'getPid' => 'pidof jackd', 
            'start' => 'jackd -R -d dummy --rate %d --period %d --capture 0 --playback 0 > %s/jackd.log 2>&1 &',
            'stop' => 'kill -kill %d',
            'join' => 'jacktrip -s --bindport %d -n %d --clientname \'%s\' > \'%s/%s.log\' 2>&1 & echo $!',
            'connect' => 'jack_connect %s:%s %s:%s', 
            'disconnect' => 'jack_disconnect %s %s',
            'snapshot' => 'aj-snapshot -fj %s/%s.snap',
            'loadClients' => 'jack_lsp',
        ],
    ]
];