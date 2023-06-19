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