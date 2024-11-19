use histoire_dont_on_est_le_hero;
Create table if not exists messages (
    id int auto_increment primary key,
    content text
);
Create table if not exists choices (
    id         int auto_increment primary key,
    message_id int,
    content    text,
    orderM      tinyint,
    message_id_next int
);
Create table if not exists path(
    id int auto_increment primary key,
    path text
);

alter table choices add constraint fk_message_id foreign key (message_id) references messages(id);
alter table choices add constraint fk_message_id_next foreign key (message_id_next) references messages(id);
