ALTER TABLE mbtiSuitableJobs
ADD CONSTRAINT fk_mbti FOREIGN KEY (id) REFERENCES mbti(id),
ADD CONSTRAINT fk_job FOREIGN KEY (id) REFERENCES job(id)



http://zeyebang.sinaapp.com/index.php/api/example/user/id/1?X-API-KEY=zeyebang_accesskey

http://datamapper.wanwizard.eu/pages/installation.html