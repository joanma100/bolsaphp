
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `usuario_id` int(20) NOT NULL auto_increment,
  `usuario_login` varchar(32) collate utf8_spanish_ci NOT NULL default '',
  `usuario_nivel` enum('usuario','admin') collate utf8_spanish_ci NOT NULL default 'usuario',
  `usuario_fecha` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `usuario_ip` varchar(32) collate utf8_spanish_ci default NULL,
  `usuario_password` varchar(64) collate utf8_spanish_ci NOT NULL default '',
  `usuario_email` varchar(128) collate utf8_spanish_ci NOT NULL default '',
  `usuario_nombre` varchar(128) collate utf8_spanish_ci NOT NULL default '',
  `usuario_url` varchar(128) collate utf8_spanish_ci NOT NULL default '',
  PRIMARY KEY  (`usuario_id`),
  UNIQUE KEY `usuario_login` (`usuario_login`),
  KEY `usuario_email` (`usuario_email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `usuarios` ( `usuario_login` , `usuario_password` , `usuario_nombre`, `usuario_nivel` ) VALUES ( 'admin', md5('admin'), 'admin', 'admin' );


DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
`log_id` INT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`log_fecha` timestamp NOT NULL default CURRENT_TIMESTAMP,
`log_usuario_id` INT( 20 ) NOT NULL ,
`log_usuario_login` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL ,
`log_descripcion` TINYTEXT CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
`log_ip` varchar(32) collate utf8_spanish_ci default NULL
) TYPE = MYISAM CHARACTER SET utf8 COLLATE utf8_spanish_ci;

