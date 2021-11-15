### codberg-nodejs-boilerplate



#### 기술 스택

- express
- sequelize
- typescript

#### Getting Started
---------------

```bash

# Install NPM dependencies
npm install

# Then simply start your app
npm start dev
```

#### .env 파일 작성

```tex
DB=
DB_PASS=
DB_NAME=
DB_HOST=
DB_USER=
dialect=mysql
```

#### DB 미사용 시 하단 코드 주석
src/index.ts 
```javascript
sequelize.sync({ force: false })
  .then(() => {
    console.log('데이터베이스 연결 성공');
  })
  .catch((err) => {
    console.error(err);
  });
```