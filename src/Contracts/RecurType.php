<?php namespace NetworkForGood\Contracts;

interface RecurType {
	const NOT_RECURRING = 'NotRecurring';
	const MONTHLY = 'Monthly';
	const QUARTERLY = 'Quarterly';
	const ANNUALLY = 'Annually';
}