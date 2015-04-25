
# 	GURPREET SINGH

import random
import csv

y = []

num_obj = 0
q = 0
with open('obj_goal.csv', 'w') as csvfile:
    fieldnames = ['obj_goal_id', 'o_id', 'g_id']
    writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
    writer.writeheader()
    for i in xrange(1, 101):
    	num_obj = random.randrange(1, 9)
    	x = []
    	for j, k in enumerate(xrange(num_obj)):
    		while (True):
	    		e = random.randrange(1, 11)
	    		if e in x:
	    			continue
	    		else:
	    			x.append(e)
	    			writer.writerow({'obj_goal_id': q, 'o_id': i, 'g_id': e})
	    			q += 1
	    			break