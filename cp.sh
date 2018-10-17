# /bin/bash

odir=$2
for i in *$1*; do
  echo "cp $i $2/$i-new"
done
