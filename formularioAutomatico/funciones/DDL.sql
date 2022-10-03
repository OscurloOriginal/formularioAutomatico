create database formularioautomatico;
	use formularioautomatico;
	create table sexo (id int auto_increment, descripcion text, primary key(id));
	insert into sexo set descripcion = 'Masculino';
	insert into sexo set descripcion = 'Femenino';
	insert into sexo set descripcion = 'UNDEFINED';