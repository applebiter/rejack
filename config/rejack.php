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
        'StreamsDir' => ROOT . DS . 'data' . DS . 'streams',
        'Commands' => [ 
            'isRunning' => 'jack_wait -c', 
            'bufsize' => 'jack_bufsize',
            'getPid' => 'pidof jackd', 
            'start' => 'jackd -R -d dummy --rate %d --period %d --capture 0 --playback 0 > %s/jackd.log 2>&1 &',
            'stop' => 'kill -kill %d',
            'join' => 'jacktrip -s --bindport %d -n %d --clientname \'%s\' > \'%s/%s.log\' 2>&1 & echo $!',
            'connect' => 'jack_connect %s:%s %s:%s', 
            'disconnect' => 'jack_disconnect %s %s',
            'snapshot' => 'aj-snapshot -qfj %s/%s.xml',
            'loadClients' => 'jack_lsp',
            'stream' => 'jack-stdout -d 0 %s | oggenc -r -R %d -B 16 -C %d -q %d - | oggfwd -n "%s" -d "%s" %s %d "%s" /%s.ogg', 
        ],
        'Default' => [ 
            'StreamConfig' => [ 
                'IcecastHost' => 'localhost', 
                'IcecastPort' => 8000, 
                'SourcePasswd' => 'hackme',
                'OggencQuality' => 3,
                'SSL' => false,
            ],
        ],
    ]
];
