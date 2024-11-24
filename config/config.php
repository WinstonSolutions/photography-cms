<?php
/**
 * Applicationonfiguration
*/
return [
  
   
   // Site configuration
   'site' => [
       'name' => 'Photography Portfolio System',
       'url' => 'http://localhost/photography-cms',
       'upload_path' => __DIR__ . '/../uploads',
       'thumbnail_path' => __DIR__ . '/../uploads/thumbnails'
   ],
   
   // Security configuration
   'security' => [
       'salt' => 'your_random_salt_here',
       'session_timeout' => 3600 // 1 hour
   ]
];