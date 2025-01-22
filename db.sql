drop database if exists isifitgems;
create database isifitgems;
use isifitgems;
set time_zone = '+01:00';

create table account (
    username varchar(255),
    password_hash varchar(255) not null,
    is_vendor boolean not null default false,
    constraint primary key (username)
);

create table category (
    code_name varchar(255),
    display_name varchar(255) not null,
    constraint primary key (code_name)
);

create table product_base (
    code_name varchar(255),
    category varchar(255) null,
    display_name varchar(255) not null,
    short_description varchar(255) not null,
    standalone boolean not null,
    constraint primary key (code_name),
    constraint foreign key (category) references category(code_name) on update cascade on delete set null
);

create table product_variant (
    base varchar(255),
    code_suffix varchar(255),
    ordinal smallint not null,
    display_name varchar(255) not null,
    color char(6) not null,
    constraint primary key (base, code_suffix),
    constraint foreign key (base) references product_base(code_name) on update cascade on delete cascade
);

create table product_info (
    id int auto_increment,
    product varchar(255) not null,
    variant varchar(255) null,
    price decimal(10, 2) not null,
    constraint primary key (id),
    constraint foreign key (product) references product_base(code_name) on update cascade on delete cascade,
    constraint foreign key (product, variant) references product_variant(base, code_suffix) on update cascade on delete cascade
);

create view price_range as
select product, min(price) as price_min, max(price) as price_max
    from product_info
    group by product;

create table bundle (
    code_name varchar(255),
    display_name varchar(255) not null,
    multiplier float not null,
    constraint primary key (code_name)
);

create table product_in_bundle (
    base varchar(255),
    bundle varchar(255),
    ordinal smallint not null,
    constraint primary key (base, bundle),
    constraint foreign key (base) references product_base(code_name) on update cascade on delete cascade,
    constraint foreign key (bundle) references bundle(code_name) on update cascade on delete cascade
);

create view bundle_variant as
with product_count_in_bundle as (
    select bundle, count(base) as product_count
        from product_in_bundle
        where base in (select base from product_variant)
        group by bundle
),
variant_count_in_bundle as (
    select bundle, code_suffix, product_variant.ordinal, count(product_in_bundle.base) as variant_count
        from product_in_bundle
        join product_variant on product_variant.base = product_in_bundle.base
        group by bundle, code_suffix
)
select variant_count_in_bundle.bundle, variant_count_in_bundle.code_suffix
    from variant_count_in_bundle
    join product_count_in_bundle on product_count_in_bundle.bundle = variant_count_in_bundle.bundle
    where variant_count = product_count
    group by variant_count_in_bundle.bundle, variant_count_in_bundle.code_suffix
    order by ordinal;

create table notification (
    id int(11) not null auto_increment,
    title varchar(255) not null,
    content varchar(255) not null,
    created_at datetime not null default current_timestamp(),
    username varchar(255) not null,
    constraint primary key (id),
    constraint foreign key (username) references account(username) on update cascade on delete cascade
);

insert into category (code_name, display_name) values
    ('bracelets', 'Bracelets'),
    ('earrings', 'Earrings'),
    ('necklaces', 'Necklaces'),
    ('pins', 'Pins'),
    ('rings', 'Rings');

insert into product_base (code_name, category, display_name, short_description, standalone) values
    ('barbed_wire_bracelet', 'bracelets', 'Barbed Wire Bracelet', 'The classic symbol of alternative culture.', true),
    ('chain_bracelet', 'bracelets', 'Chain Bracelet', 'Stylish and fashionable with a touch of edginess.', true),
    ('chain_necklace', 'necklaces', 'Chain Necklace', 'A necklace. Made of chain.', true),
    ('choker', 'necklaces', 'Choker', 'Why so serious?', true),
    ('colorful_ring', 'rings', 'Colorful Ring', 'A ring which happens to be colorful.', false),
    ('colorful_string_bracelet', 'bracelets', 'Colorful String Bracelet', 'A bracelet which is made of string and is colorful.', false),
    ('dagger_earrings', 'earrings', 'Dagger Earrings', 'Not very effective for stabbing people.', true),
    ('dragon_pendant', 'necklaces', 'Dragon Pendant', 'Dragon deez nuts.', true),
    ('flame_earrings', 'earrings', 'Flame Earrings', 'Not made of actual fire.', true),
    ('flower_bracelet', 'bracelets', 'Flower Bracelet', 'It is recommended to water the flowers daily to keep them from withering.', false),
    ('gem_earrings', 'earrings', 'Gem Earrings', 'Whether you believe in their power, gems are sure to complement your attire.', true),
    ('impostor_pin', 'pins', 'Impostor Pin', 'Might make you look a bit sus.', true),
    ('leather_bracelet', 'bracelets', 'Leather Bracelet', 'Cows were definitely harmed to make this product.', false),
    ('moai_pin', 'pins', 'Moai Pin', '🗿.', true),
    ('pebble_bracelet', 'bracelets', 'Pebble Bracelet', 'Did you know a group of pebbles is called a stoner?', false),
    ('raven_pendant', 'necklaces', 'Raven Pendant', 'The spirit animal of goth culture.', true),
    ('simple_bracelet', 'bracelets', 'Simple Bracelet', 'Less is more.', true),
    ('simple_ring', 'rings', 'Simple Ring', 'A classic minimal design, suitable for all occasions.', true),
    ('skull_ring', 'rings', 'Skull Ring', 'A staple of macabre accessories.', true),
    ('snake_ring', 'rings', 'Snake Ring', 'Ssssss, sssss sss ssssss.', true);

insert into product_variant (base, code_suffix, ordinal, display_name, color) values
    ('colorful_ring', 'red', 0, 'Red', 'C51111'),
    ('colorful_ring', 'blue', 1, 'Blue', '132ED1'),
    ('colorful_ring', 'green', 2, 'Green', '117F2D'),
    ('colorful_ring', 'pink', 3, 'Pink', 'ED54BA'),
    ('colorful_ring', 'orange', 4, 'Orange', 'EF7D0D'),
    ('colorful_ring', 'yellow', 5, 'Yellow', 'F5F557'),
    ('colorful_ring', 'black', 6, 'Black', '3F474E'),
    ('colorful_ring', 'white', 7, 'White', 'D6E0F0'),
    ('colorful_ring', 'purple', 8, 'Purple', '6B2FBB'),
    ('colorful_ring', 'brown', 9, 'Brown', '71491E'),
    ('colorful_ring', 'cyan', 10, 'Cyan', '38FADC'),
    ('colorful_ring', 'lime', 11, 'Lime', '50EF39'),
    ('colorful_string_bracelet', 'red', 0, 'Red', 'C51111'),
    ('colorful_string_bracelet', 'blue', 1, 'Blue', '132ED1'),
    ('colorful_string_bracelet', 'green', 2, 'Green', '117F2D'),
    ('colorful_string_bracelet', 'pink', 3, 'Pink', 'ED54BA'),
    ('colorful_string_bracelet', 'orange', 4, 'Orange', 'EF7D0D'),
    ('colorful_string_bracelet', 'yellow', 5, 'Yellow', 'F5F557'),
    ('colorful_string_bracelet', 'black', 6, 'Black', '3F474E'),
    ('colorful_string_bracelet', 'white', 7, 'White', 'D6E0F0'),
    ('colorful_string_bracelet', 'purple', 8, 'Purple', '6B2FBB'),
    ('colorful_string_bracelet', 'brown', 9, 'Brown', '71491E'),
    ('colorful_string_bracelet', 'cyan', 10, 'Cyan', '38FADC'),
    ('colorful_string_bracelet', 'lime', 11, 'Lime', '50EF39'),
    ('impostor_pin', 'red', 0, 'Red', 'C51111'),
    ('impostor_pin', 'blue', 1, 'Blue', '132ED1'),
    ('impostor_pin', 'green', 2, 'Green', '117F2D'),
    ('impostor_pin', 'pink', 3, 'Pink', 'ED54BA'),
    ('impostor_pin', 'orange', 4, 'Orange', 'EF7D0D'),
    ('impostor_pin', 'yellow', 5, 'Yellow', 'F5F557'),
    ('impostor_pin', 'black', 6, 'Black', '3F474E'),
    ('impostor_pin', 'white', 7, 'White', 'D6E0F0'),
    ('impostor_pin', 'purple', 8, 'Purple', '6B2FBB'),
    ('impostor_pin', 'brown', 9, 'Brown', '71491E'),
    ('impostor_pin', 'cyan', 10, 'Cyan', '38FADC'),
    ('impostor_pin', 'lime', 11, 'Lime', '50EF39'),
    ('barbed_wire_bracelet', 'black_steel', 0, 'Black Steel', '181818'),
    ('barbed_wire_bracelet', 'chrome', 1, 'Chrome', 'A0A0A0'),
    ('chain_bracelet', 'gunmetal', 0, 'Gunmetal', '2C2C2C'),
    ('chain_bracelet', 'copper', 1, 'Copper', 'C68346'),
    ('chain_bracelet', 'black_steel', 2, 'Black Steel', '181818'),
    ('chain_necklace', 'gunmetal', 0, 'Gunmetal', '2C2C2C'),
    ('chain_necklace', 'black_steel', 1, 'Black Steel', '181818'),
    ('dragon_pendant', 'gunmetal', 0, 'Gunmetal', '2C2C2C'),
    ('dragon_pendant', 'copper', 1, 'Copper', 'C68346'),
    ('dragon_pendant', 'black_steel', 2, 'Black Steel', '181818'),
    ('flame_earrings', 'copper', 0, 'Copper', 'C68346'),
    ('flame_earrings', 'black_steel', 1, 'Black Steel', '181818'),
    ('simple_bracelet', 'gunmetal', 0, 'Gunmetal', '2C2C2C'),
    ('simple_bracelet', 'copper', 1, 'Copper', 'C68346'),
    ('simple_bracelet', 'black_steel', 2, 'Black Steel', '181818'),
    ('simple_ring', 'gunmetal', 0, 'Gunmetal', '2C2C2C'),
    ('simple_ring', 'copper', 1, 'Copper', 'C68346'),
    ('simple_ring', 'black_steel', 2, 'Black Steel', '181818'),
    ('skull_ring', 'gunmetal', 0, 'Gunmetal', '2C2C2C'),
    ('skull_ring', 'copper', 1, 'Copper', 'C68346'),
    ('skull_ring', 'black_steel', 2, 'Black Steel', '181818'),
    ('snake_ring', 'gunmetal', 0, 'Gunmetal', '2C2C2C'),
    ('snake_ring', 'black_steel', 1, 'Black Steel', '181818'),
    ('gem_earrings', 'ruby', 0, 'Ruby', '800020'),
    ('gem_earrings', 'obsidian', 1, 'Obsidian', '1C1020'),
    ('gem_earrings', 'demon_core', 2, 'Demon Core', '808080');

insert into product_info (product, variant, price) values
    ('choker', null, 1.00),
    ('dagger_earrings', null, 1.00),
    ('flower_bracelet', null, 1.00),
    ('leather_bracelet', null, 1.00),
    ('moai_pin', null, 1000.00),
    ('pebble_bracelet', null, 1.00),
    ('raven_pendant', null, 7.00),
    ('colorful_ring', 'red', 1.00),
    ('colorful_ring', 'blue', 1.00),
    ('colorful_ring', 'green', 1.00),
    ('colorful_ring', 'pink', 1.00),
    ('colorful_ring', 'orange', 1.00),
    ('colorful_ring', 'yellow', 1.00),
    ('colorful_ring', 'black', 1.00),
    ('colorful_ring', 'white', 1.00),
    ('colorful_ring', 'purple', 1.00),
    ('colorful_ring', 'brown', 1.00),
    ('colorful_ring', 'cyan', 1.00),
    ('colorful_ring', 'lime', 1.00),
    ('colorful_string_bracelet', 'red', 1.00),
    ('colorful_string_bracelet', 'blue', 1.00),
    ('colorful_string_bracelet', 'green', 1.00),
    ('colorful_string_bracelet', 'pink', 1.00),
    ('colorful_string_bracelet', 'orange', 1.00),
    ('colorful_string_bracelet', 'yellow', 1.00),
    ('colorful_string_bracelet', 'black', 1.00),
    ('colorful_string_bracelet', 'white', 1.00),
    ('colorful_string_bracelet', 'purple', 1.00),
    ('colorful_string_bracelet', 'brown', 1.00),
    ('colorful_string_bracelet', 'cyan', 1.00),
    ('colorful_string_bracelet', 'lime', 1.00),
    ('impostor_pin', 'red', 1.00),
    ('impostor_pin', 'blue', 1.00),
    ('impostor_pin', 'green', 1.00),
    ('impostor_pin', 'pink', 1.00),
    ('impostor_pin', 'orange', 1.00),
    ('impostor_pin', 'yellow', 1.00),
    ('impostor_pin', 'black', 1.00),
    ('impostor_pin', 'white', 1.00),
    ('impostor_pin', 'purple', 1.00),
    ('impostor_pin', 'brown', 1.00),
    ('impostor_pin', 'cyan', 1.00),
    ('impostor_pin', 'lime', 1.00),
    ('barbed_wire_bracelet', 'black_steel', 1.00),
    ('barbed_wire_bracelet', 'chrome', 1.00),
    ('chain_bracelet', 'gunmetal', 1.00),
    ('chain_bracelet', 'copper', 1.00),
    ('chain_bracelet', 'black_steel', 1.00),
    ('chain_necklace', 'gunmetal', 2.00),
    ('chain_necklace', 'black_steel', 3.00),
    ('dragon_pendant', 'gunmetal', 2.00),
    ('dragon_pendant', 'copper', 1.00),
    ('dragon_pendant', 'black_steel', 2.00),
    ('flame_earrings', 'copper', 1.00),
    ('flame_earrings', 'black_steel', 1.00),
    ('simple_bracelet', 'gunmetal', 1.00),
    ('simple_bracelet', 'copper', 1.00),
    ('simple_bracelet', 'black_steel', 1.00),
    ('simple_ring', 'gunmetal', 1.00),
    ('simple_ring', 'copper', 1.00),
    ('simple_ring', 'black_steel', 1.00),
    ('skull_ring', 'gunmetal', 1.00),
    ('skull_ring', 'copper', 1.00),
    ('skull_ring', 'black_steel', 1.00),
    ('snake_ring', 'gunmetal', 1.00),
    ('snake_ring', 'black_steel', 1.00),
    ('gem_earrings', 'ruby', 1.00),
    ('gem_earrings', 'obsidian', 1.00),
    ('gem_earrings', 'demon_core', 0.00);

insert into bundle (code_name, display_name, multiplier) values
    ('suspicious_bundle', 'Suspicious Bundle', 0.9),
    ('dragon_bundle', 'Dragon Bundle', 0.8),
    ('moai_bundle', 'Moai Bundle', 0.9),
    ('goth_bundle', 'Goth Bundle', 0.7),
    ('goth_bundle_plus', 'Goth Bundle+', 0.7);

insert into product_in_bundle (base, bundle, ordinal) values
    ('impostor_pin', 'suspicious_bundle', 0),
    ('colorful_string_bracelet', 'suspicious_bundle', 1),
    ('colorful_ring', 'suspicious_bundle', 2),
    ('moai_pin', 'moai_bundle', 0),
    ('flower_bracelet', 'moai_bundle', 1),
    ('pebble_bracelet', 'moai_bundle', 2),
    ('dragon_pendant', 'dragon_bundle', 0),
    ('flame_earrings', 'dragon_bundle', 1),
    ('raven_pendant', 'goth_bundle', 0),
    ('choker', 'goth_bundle', 1),
    ('dagger_earrings', 'goth_bundle', 2),
    ('simple_ring', 'goth_bundle_plus', 0),
    ('raven_pendant', 'goth_bundle_plus', 1),
    ('choker', 'goth_bundle_plus', 2),
    ('barbed_wire_bracelet', 'goth_bundle_plus', 3),
    ('dagger_earrings', 'goth_bundle_plus', 3);
