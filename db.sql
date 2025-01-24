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
    price_base decimal(10, 2),
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
    price_override decimal(10, 2) null,
    constraint primary key (base, code_suffix),
    constraint foreign key (base) references product_base(code_name) on update cascade on delete cascade
);

create view price_range as
select pb.code_name as base,
    coalesce(min(case when pv.price_override is not null then pv.price_override else pb.price_base end), pb.price_base) as min_price,
    coalesce(max(case when pv.price_override is not null then pv.price_override else pb.price_base end), pb.price_base) as max_price
from product_base pb
left join product_variant pv on pb.code_name = pv.base
group by pb.code_name, pb.display_name, pb.price_base;

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

create view bundle_price as
with bundle_price_before_discount as (
    select bundle.code_name as bundle, bundle_variant.code_suffix, 
        sum(case 
                when product_variant.price_override is not null then product_variant.price_override
                else product_base.price_base
            end
        ) as price_before_discount, bundle.multiplier
    from bundle
    join product_in_bundle on product_in_bundle.bundle = bundle.code_name 
    left join bundle_variant on bundle_variant.bundle = bundle.code_name
    join product_base on product_base.code_name = product_in_bundle.base
    left join product_variant on product_variant.base = product_in_bundle.base and product_variant.code_suffix = bundle_variant.code_suffix
    group by bundle.code_name, bundle_variant.code_suffix, bundle.multiplier
)
select bundle, code_suffix as variant,price_before_discount, round(multiplier * price_before_discount, 2) as price_with_discount
from bundle_price_before_discount;

create table notification (
    id int(11) not null auto_increment,
    title varchar(255) not null,
    content varchar(255) not null,
    created_at datetime not null default current_timestamp(),
    username varchar(255) not null,
    constraint primary key (id),
    constraint foreign key (username) references account(username) on update cascade on delete cascade
);

insert into account(username, password_hash, is_vendor) values 
    ('Vendor', '$2y$10$d.wSTHXbqliWN3rHnXY6LeodHpP0ClE55diZpFPqW6mpHubcLgHG2', 1); -- Vendor account, Password: Vend0r!!!

insert into category (code_name, display_name) values
    ('bracelets', 'Bracelets'),
    ('earrings', 'Earrings'),
    ('necklaces', 'Necklaces'),
    ('pins', 'Pins'),
    ('rings', 'Rings');

insert into product_base (code_name, category, display_name, short_description, price_base, standalone) values
    ('barbed_wire_bracelet', 'bracelets', 'Barbed Wire Bracelet', 'The classic symbol of alternative culture.', 1.00, true),
    ('chain_bracelet', 'bracelets', 'Chain Bracelet', 'Stylish and fashionable with a touch of edginess.', 1.00, true),
    ('chain_necklace', 'necklaces', 'Chain Necklace', 'A necklace. Made of chain.', 1.00, true),
    ('choker', 'necklaces', 'Choker', 'Why so serious?', 1.00, true),
    ('colorful_ring', 'rings', 'Colorful Ring', 'A ring which happens to be colorful.', 1.00, false),
    ('colorful_string_bracelet', 'bracelets', 'Colorful String Bracelet', 'A bracelet which is made of string and is colorful.', 1.00, false),
    ('dagger_earrings', 'earrings', 'Dagger Earrings', 'Not very effective for stabbing people.', 1.00, true),
    ('dragon_pendant', 'necklaces', 'Dragon Pendant', 'Dragon deez nuts.', 1.00, true),
    ('flame_earrings', 'earrings', 'Flame Earrings', 'Not made of actual fire.', 1.00, true),
    ('flower_bracelet', 'bracelets', 'Flower Bracelet', 'It is recommended to water the flowers daily to keep them from withering.', 1.00, false),
    ('gem_earrings', 'earrings', 'Gem Earrings', 'Whether you believe in their power, gems are sure to complement your attire.', 1.00, true),
    ('impostor_pin', 'pins', 'Impostor Pin', 'Might make you look a bit sus.', 1.00, true),
    ('leather_bracelet', 'bracelets', 'Leather Bracelet', 'Cows were definitely harmed to make this product.', 1.00, false),
    ('moai_pin', 'pins', 'Moai Pin', '🗿.', 1.00, true),
    ('pebble_bracelet', 'bracelets', 'Pebble Bracelet', 'Did you know a group of pebbles is called a stoner?', 1.00, false),
    ('raven_pendant', 'necklaces', 'Raven Pendant', 'The spirit animal of goth culture.', 1.00, true),
    ('simple_bracelet', 'bracelets', 'Simple Bracelet', 'Less is more.', 1.00, true),
    ('simple_ring', 'rings', 'Simple Ring', 'A classic minimal design, suitable for all occasions.', 1.00, true),
    ('skull_ring', 'rings', 'Skull Ring', 'A staple of macabre accessories.', 1.00, true),
    ('snake_ring', 'rings', 'Snake Ring', 'Ssssss, sssss sss ssssss.', 1.00, true);

insert into product_variant (base, code_suffix, ordinal, display_name, color, price_override) values
    ('colorful_ring', 'red', 0, 'Red', 'C51111', 1.0),
    ('colorful_ring', 'blue', 1, 'Blue', '132ED1', 1.0),
    ('colorful_ring', 'green', 2, 'Green', '117F2D', 1.0),
    ('colorful_ring', 'pink', 3, 'Pink', 'ED54BA', 1.0),
    ('colorful_ring', 'orange', 4, 'Orange', 'EF7D0D', 1.0),
    ('colorful_ring', 'yellow', 5, 'Yellow', 'F5F557', 1.0),
    ('colorful_ring', 'black', 6, 'Black', '3F474E', 1.0),
    ('colorful_ring', 'white', 7, 'White', 'D6E0F0', 1.0),
    ('colorful_ring', 'purple', 8, 'Purple', '6B2FBB', 1.0),
    ('colorful_ring', 'brown', 9, 'Brown', '71491E', 1.0),
    ('colorful_ring', 'cyan', 10, 'Cyan', '38FADC', 1.0),
    ('colorful_ring', 'lime', 11, 'Lime', '50EF39', 1.0),
    ('colorful_string_bracelet', 'red', 0, 'Red', 'C51111', 1.0),
    ('colorful_string_bracelet', 'blue', 1, 'Blue', '132ED1', 1.0),
    ('colorful_string_bracelet', 'green', 2, 'Green', '117F2D', 1.0),
    ('colorful_string_bracelet', 'pink', 3, 'Pink', 'ED54BA', 1.0),
    ('colorful_string_bracelet', 'orange', 4, 'Orange', 'EF7D0D', 1.0),
    ('colorful_string_bracelet', 'yellow', 5, 'Yellow', 'F5F557', 1.0),
    ('colorful_string_bracelet', 'black', 6, 'Black', '3F474E', 1.0),
    ('colorful_string_bracelet', 'white', 7, 'White', 'D6E0F0', 1.0),
    ('colorful_string_bracelet', 'purple', 8, 'Purple', '6B2FBB', 1.0),
    ('colorful_string_bracelet', 'brown', 9, 'Brown', '71491E', 1.0),
    ('colorful_string_bracelet', 'cyan', 10, 'Cyan', '38FADC', 1.0),
    ('colorful_string_bracelet', 'lime', 11, 'Lime', '50EF39', 1.0),
    ('impostor_pin', 'red', 0, 'Red', 'C51111', 1.0),
    ('impostor_pin', 'blue', 1, 'Blue', '132ED1', 1.0),
    ('impostor_pin', 'green', 2, 'Green', '117F2D', 1.0),
    ('impostor_pin', 'pink', 3, 'Pink', 'ED54BA', 1.0),
    ('impostor_pin', 'orange', 4, 'Orange', 'EF7D0D', 1.0),
    ('impostor_pin', 'yellow', 5, 'Yellow', 'F5F557', 1.0),
    ('impostor_pin', 'black', 6, 'Black', '3F474E', 1.0),
    ('impostor_pin', 'white', 7, 'White', 'D6E0F0', 1.0),
    ('impostor_pin', 'purple', 8, 'Purple', '6B2FBB', 1.0),
    ('impostor_pin', 'brown', 9, 'Brown', '71491E', 1.0),
    ('impostor_pin', 'cyan', 10, 'Cyan', '38FADC', 1.0),
    ('impostor_pin', 'lime', 11, 'Lime', '50EF39', 1.0),
    ('barbed_wire_bracelet', 'black_steel', 0, 'Black Steel', '181818', 1.0),
    ('barbed_wire_bracelet', 'chrome', 1, 'Chrome', 'A0A0A0', 1.0),
    ('chain_bracelet', 'gunmetal', 0, 'Gunmetal', '2C2C2C', 1.0),
    ('chain_bracelet', 'copper', 1, 'Copper', 'C68346', 1.0),
    ('chain_bracelet', 'black_steel', 2, 'Black Steel', '181818', 1.0),
    ('chain_necklace', 'gunmetal', 0, 'Gunmetal', '2C2C2C', 1.0),
    ('chain_necklace', 'black_steel', 1, 'Black Steel', '181818', 1.0),
    ('dragon_pendant', 'gunmetal', 0, 'Gunmetal', '2C2C2C', 1.0),
    ('dragon_pendant', 'copper', 1, 'Copper', 'C68346', 1.0),
    ('dragon_pendant', 'black_steel', 2, 'Black Steel', '181818', 1.0),
    ('flame_earrings', 'copper', 0, 'Copper', 'C68346', 1.0),
    ('flame_earrings', 'black_steel', 1, 'Black Steel', '181818', 1.0),
    ('simple_bracelet', 'gunmetal', 0, 'Gunmetal', '2C2C2C', 1.0),
    ('simple_bracelet', 'copper', 1, 'Copper', 'C68346', 1.0),
    ('simple_bracelet', 'black_steel', 2, 'Black Steel', '181818', 1.0),
    ('simple_ring', 'gunmetal', 0, 'Gunmetal', '2C2C2C', 1.0),
    ('simple_ring', 'copper', 1, 'Copper', 'C68346', 1.0),
    ('simple_ring', 'black_steel', 2, 'Black Steel', '181818', 1.0),
    ('skull_ring', 'gunmetal', 0, 'Gunmetal', '2C2C2C', 1.0),
    ('skull_ring', 'copper', 1, 'Copper', 'C68346', 1.0),
    ('skull_ring', 'black_steel', 2, 'Black Steel', '181818', 1.0),
    ('snake_ring', 'gunmetal', 0, 'Gunmetal', '2C2C2C', 1.0),
    ('snake_ring', 'black_steel', 1, 'Black Steel', '181818', 1.0),
    ('gem_earrings', 'ruby', 0, 'Ruby', '800020', 1.0),
    ('gem_earrings', 'obsidian', 1, 'Obsidian', '1C1020', 1.0),
    ('gem_earrings', 'demon_core', 2, 'Demon Core', '808080', 1.0);

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

insert into notification (title, content, username) values
    ('Dummy title', 'This is a dummy notification.', 'Vendor'),
    ('Example', 'This is an example of a notification with a longer text content. It should be helpful in seeing how the layout works.', 'Vendor');
