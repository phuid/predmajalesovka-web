CREATE TABLE proofs (
    id int NOT NULL,
    round_id int NOT NULL,
    team_id int NOT NULL,
    time TIMESTAMP NOT NULL,
    img_url varchar(255) NOT NULL,
    verified boolean,
    PRIMARY KEY (id)
);
CREATE TABLE rounds (
    id int NOT NULL,
    nickname varchar(255) NOT NULL,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP DEFAULT '2024-05-01 00:00:00',
    hint_folder varchar(255) NOT NULL,
    PRIMARY KEY (id)
);