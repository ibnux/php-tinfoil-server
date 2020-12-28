CREATE TABLE t_games_url (
                url         VARCHAR (128) PRIMARY KEY,
                filename    VARCHAR (256) DEFAULT '',
                title       VARCHAR (256) DEFAULT '',
                titleid     VARCHAR (16)  DEFAULT '',
                fileSize    VARCHAR (128) DEFAULT '0',
                md5Checksum VARCHAR (64)  DEFAULT '',
                folder      VARCHAR (32)  DEFAULT '',
                root        VARCHAR (64)  DEFAULT '',
                owner       VARCHAR (64)  DEFAULT '',
                shared      TINYINT (1)   DEFAULT '1'
            );

CREATE TABLE t_games (
                titleid     VARCHAR (16)  PRIMARY KEY NOT NULL,
                name        VARCHAR (256) NOT NULL,
                image       VARCHAR (512) NOT NULL
                                          DEFAULT '',
                description TEXT          NOT NULL,
                publisher   VARCHAR (64)  NOT NULL,
                languages   VARCHAR (256) NOT NULL,
                rating      TINYINT (1)   NOT NULL
                                          DEFAULT '0',
                size        VARCHAR (16)  NOT NULL
            );