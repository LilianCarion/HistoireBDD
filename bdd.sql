use histoire_dont_on_est_le_hero;
Create table if not exists messages (
    id int auto_increment primary key,
    content text
);
Create table if not exists choices (
    id         int auto_increment primary key,
    message_id int,
    content    text,
    message_id_next int
);
Create table if not exists path(
    id int auto_increment primary key,
    path text
);

alter table choices add constraint fk_message_id foreign key (message_id) references messages(id);
alter table choices add constraint fk_message_id_next foreign key (message_id_next) references messages(id);

create trigger after_update_path_trigger
    before update on path
    for each row
begin
    begin
        if new.path not like '%;%' and new.path != '1' then
            set new.path = concat(old.path, ';', new.path);
        end if;
    end;
end;