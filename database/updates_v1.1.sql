-- Create programs table:
create table programs(
    id int auto_increment primary key,
    name VARCHAR(100) not null,
    root_folder_path VARCHAR(199) NOT null,
    created_by int not null,
    created_at TIMESTAMP null DEFAULT current_timestamp(),
    updated_at TIMESTAMP null DEFAULT current_timestamp() on update current_timestamp(),
    constraint unique_program_name UNIQUE(name),
    constraint unique_root_folder_path UNIQUE(root_folder_path),
    constraint fk_program_created_by FOREIGN KEY(created_by) references users(id) on update cascade on delete restrict
);

LOCK TABLES `programs` WRITE;
/*!40000 ALTER TABLE `programs` DISABLE KEYS */;
INSERT INTO `programs` VALUES (1,'System Default','SYSTEM BACKUP/EVENT MANAGER/',1,'2023-06-19 09:27:39','2023-06-19 10:08:10'),(2,'CONNECT','SYSTEM BACKUP/EVENT MANAGER/CONNECT/',1,'2023-06-19 10:09:15','2023-06-19 10:09:42');
/*!40000 ALTER TABLE `programs` ENABLE KEYS */;
UNLOCK TABLES;

-- Add programs to system
alter table systems add column program_id int null after folder_id;
update systems set program_id = 2 where 1;
ALTER table systems change column program_id program_id int not null;

-- Add programs to users
alter table users add column program_id int null after access_level;
update users set program_id = 2 where 1;
alter table users change program_id program_id int not null,
    change access_level access_level enum('Admin', 'Program', 'Facility') not null default 'Facility';
